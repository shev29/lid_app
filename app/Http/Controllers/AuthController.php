<?php

namespace App\Http\Controllers;

use App\Models\MasterEmailAccount;
use App\Models\MasterEmployees;
use App\Models\User;
use App\Services\SafeToken;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Drivers\Gd\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class AuthController extends Controller
{
    public function login(Request $request) {
        // if ($request->session()->has('token')) {
        //     $decrypted = Crypt::decryptString($request->session()->get('token'));
        //     $token = json_decode($decrypted, true);
        //     if (isset($token['userNik']) && isset($token['userName'])) {
        //         return redirect()->route('home');
        //     }
        // }

        $redirectUri = $request->query('redirect_uri');
        return view('auth.login', compact('redirectUri'));
    }

    public function auth_login(Request $request) {
        $request->validate([
            'user_name' => 'required',
            'password' => 'required',
        ]);

        // $user = User::from('users as a')
        //             ->leftJoin('vw_master_employee_active as b', 'a.employee_id', '=', 'b.employee_id')
        //             ->leftJoin('master_role_users as c', function($join) {
        //                 $join->on('c.employee_id', 'a.employee_id')
        //                      ->where('c.role_type', 'MENUS');
        //             })
        //             ->select('a.*', 'b.department_code', 'b.location_code', 'b.avatar', 'c.user_role_id', 'c.role_id')
        //             ->where(function($query) use ($request) {
        //                 $query->where('a.user_name', $request->user_name)
        //                       ->orWhere('a.email', $request->user_name);
        //             })
        //             ->where('b.is_active', 1)
        //             ->first();
        $user = User::from('users as a')
                    ->leftJoin('vw_master_employee_active as b', function($join) {
                        $join->on('b.employee_id', 'a.employee_id');
                            // ->on('b.company_id', 'a.company_id');
                    })
                    ->leftJoin('master_employees_multi_company as c', function($join) {
                        $join->on('c.employee_id', 'a.employee_id')
                            ->where('c.is_main', '1')
                            ->where('c.is_active', '1');
                    })
                    // ->leftJoin('master_department as d', 'd.department_id', 'b.department_id')
                    // ->leftJoin('master_locations as e', 'e.location_id', 'b.location_id')
                    ->select(
                        'a.id',
                        'a.employee_id',
                        'a.role_id',
                        'b.employee_name',
                        'b.employee_email',
                        DB::raw('COALESCE(c.company_id, b.company_id) as company_id'), // <-- JIKA TIDAK ADA DI MULTI COMPANY, MAKA COMPANY_ID NYA DARI MASTER_EMPLOYEES
                        'b.avatar',
                        'a.password',
                        'a.login_attempts',
                        'a.lockout_time'
                        // 'd.department_code',
                        // 'e.location_code'
                    )
                    ->where(function($query) use ($request) {
                        $query->where('a.user_name', $request->user_name)
                              ->orWhere('a.email', $request->user_name);
                    })
                    ->where('b.is_active', '1')
                    ->first();

        if($user){
            // dd($user);
            $redirect = route('home');
            $redirectUri = $request->input('redirect_uri');
            if ($redirectUri) {
                $decodedUri = urldecode($redirectUri);
                $decodedUri = urldecode($decodedUri);

                if (filter_var($decodedUri, FILTER_VALIDATE_URL)) {
                    $redirect = $decodedUri;
                }
            }


            if ($user->lockout_time && $user->lockout_time->isFuture()) {
                return response()->json([
                    'status' => false,
                    'key' => 'user_name',
                    'message' => 'Account locked. Try again later.'
                ], 403);
            }

            // $getPassword = User::select('password', 'password_old')
            //                     ->where('user_name', $request->user_name)
            //                     ->where('is_active', '=', 1)
            //                     ->first();

            if($user->password == null){
                // OLD LOGIN
                $passmd5 = md5($request->password);
                $loginOld = User::where(function($query) use ($request) {
                                        $query->where('user_name', $request->user_name)
                                            ->orWhere('email', $request->user_name);
                                    })
                                    ->where('password_old', $passmd5)
                                    ->where('is_active', '=', 1)
                                    ->first();
                if($loginOld){
                    $user->login_attempts = 0;
                    $user->lockout_time = null;
                    $user->password = Hash::make($request->password);
                    $user->save();

                    Auth::login($user, $request->remember);
                    $request->session()->regenerate();
                    // return response()->json(['message' => 'Login successful', 'redirect' => route('home')], 200);
                    // $redirect = session('url.intended', route('home'));
                    // $request->session()->forget('url.intended');

                    // // CREATE LEAVE SESSION
                    // session([
                    //     'username' => $user->user_name,
                    //     'nik' => $user->employee_id,
                    //     'level_akses' => $user->leave_level,
                    //     'nama' => $user->employee_name,
                    //     'kode_loc' => $user->location_code,
                    //     'kode_dep' => $user->department_code,
                    //     'kode_section' => $user->section_name,
                    //     'token' =>  $user->token_leave,
                    //     'level' => $user->level_name,
                    // ]);
                    // if($user->leave_level == 'admin'){
                    //     session([
                    //                 'username' => $user->user_name,
                    //                 'nik' => $user->employee_id,
                    //                 'level_akses' => $user->leave_level,
                    //                 'nama' => $user->employee_name,
                    //                 'kode_loc' => $user->location_code,
                    //                 'kode_dep' => $user->department_code,
                    //                 'kode_section' => $user->section_name,
                    //                 'token' =>  $user->token_leave,
                    //                 'level' => $user->level_name,
                    //             ]);
                    // }
                    // else if($user->leave_level == 'user'){
                    //     session([
                    //         'username' => $user->user_name,
                    //         'nik' => $user->employee_id,
                    //         'level_akses' => $user->leave_level,
                    //         'nama' => $user->employee_name,
                    //         'kode_loc' => $user->location_code,
                    //         'kode_dep' => $user->department_code,
                    //         'kode_section' => $user->section_name,
                    //         'token' =>  $user->token_leave,
                    //         'level' => $user->level_name,
                    //     ]);
                    // }
                    // else if($user->leave_level == 'hrga'){
                    //     session([
                    //         'username' => $user->user_name,
                    //         'nik' => $user->employee_id,
                    //         'level_akses' => $user->leave_level,
                    //     ]);
                    // }
                    // // END LEAVE SESSION

                    if(!$user->avatar) {
                        $this->createAvatar(['employeeName' => Str::upper($user->employee_name), 'employeeId' => $user->employee_id, 'employeeEmail' => $user->employee_email]);
                    }

                    return response()->json([
                        'status' => true,
                        'key' => null,
                        'message' => 'Login successful',
                        'redirect' => $redirect,
                    ], 200);
                }
                else{
                    $user->increment('login_attempts');
                    if ($user->login_attempts >= 5) {
                        $user->lockout_time = now()->addMinutes(5); // Lockout for 5 minutes
                        $user->save();
                        return response()->json(['message' => 'Account locked. Try again later.'], 403);
                    }
                    else{
                        return response()->json([
                            'status' => false,
                            'key' => 'password',
                            'message' => 'Incorrect password'
                        ], 401);
                    }

                    $user->save();
                }
            }
            else{
                // NEW LOGIN
                if (Hash::check($request->password, $user->password)) {
                    // $data = ['userNik' =>  $user->user_nik, 'userName' => $user->user_name];
                    // $encrypted = Crypt::encryptString(json_encode($data));

                    // $request->session()->put('token', $encrypted);
                    // $request->session()->put('userNik', $user->user_nik);
                    // $request->session()->put('userName', $user->user_name);
                    // // return redirect()->route('home');

                    // $redirectUrl = $request->session()->get('url.intended', route('home'));
                    // $request->session()->forget('url.intended');

                    // return response()->json([
                    //     'status' => true,
                    //     'key' => null,
                    //     'message' => 'Successfully sign in',
                    //     'redirectUrl' => $redirectUrl
                    // ]);

                    $user->login_attempts = 0;
                    $user->lockout_time = null;
                    $user->save();

                    Auth::login($user, $request->remember);
                    $request->session()->regenerate();
                    // return response()->json(['message' => 'Login successful', 'redirect' => route('home')], 200);
                    // $redirect = session('url.intended', route('home'));
                    // $request->session()->forget('url.intended');

                    // // CREATE LEAVE SESSION
                    // session([
                    //     'username' => $user->user_name,
                    //     'nik' => $user->employee_id,
                    //     'level_akses' => $user->leave_level,
                    //     'nama' => $user->employee_name,
                    //     'kode_loc' => $user->location_code,
                    //     'kode_dep' => $user->department_code,
                    //     'kode_section' => $user->section_name,
                    //     'token' =>  $user->token_leave,
                    //     'level' => $user->level_name,
                    // ]);
                    // if($user->leave_level == 'admin'){
                    //     session([
                    //                 'username' => $user->user_name,
                    //                 'nik' => $user->employee_id,
                    //                 'level_akses' => $user->leave_level,
                    //                 'nama' => $user->employee_name,
                    //                 'kode_loc' => $user->location_code,
                    //                 'kode_dep' => $user->department_code,
                    //                 'kode_section' => $user->section_name,
                    //                 'token' =>  $user->token_leave,
                    //                 'level' => $user->level_name,
                    //             ]);
                    // }
                    // else if($user->leave_level == 'user'){
                    //     session([
                    //         'username' => $user->user_name,
                    //         'nik' => $user->employee_id,
                    //         'level_akses' => $user->leave_level,
                    //         'nama' => $user->employee_name,
                    //         'kode_loc' => $user->location_code,
                    //         'kode_dep' => $user->department_code,
                    //         'kode_section' => $user->section_name,
                    //         'token' =>  $user->token_leave,
                    //         'level' => $user->level_name,
                    //     ]);
                    // }
                    // else if($user->leave_level == 'hrga'){
                    //     session([
                    //         'username' => $user->user_name,
                    //         'nik' => $user->employee_id,
                    //         'level_akses' => $user->leave_level,
                    //     ]);
                    // }
                    // // END LEAVE SESSION

                    if(!$user->avatar) {
                        $this->createAvatar(['employeeName' => Str::upper($user->employee_name), 'employeeId' => $user->employee_id, 'employeeEmail' => $user->employee_email]);
                    }

                    return response()->json([
                        'status' => true,
                        'key' => null,
                        'message' => 'Login successful',
                        'redirect' => $redirect,
                    ], 200);
                }
                else{
                    $user->increment('login_attempts');
                    if ($user->login_attempts >= 5) {
                        $user->lockout_time = now()->addMinutes(5); // Lockout for 5 minutes
                        $user->save();
                        return response()->json(['message' => 'Account locked. Try again later.'], 403);
                    }
                    else{
                        return response()->json([
                            'status' => false,
                            'key' => 'password',
                            'message' => 'Incorrect password'
                        ], 401);
                    }

                    $user->save();
                }
            }
        }
        else{
            $masterEmployee = MasterEmployees::select(
                                                'employee_id',
                                                'employee_name',
                                                'employee_email',
                                                'company_id'
                                            )
                                            ->where('employee_email', $request->user_name)
                                            ->where('is_active', '1')
                                            ->first();

            if ($masterEmployee) {
                $userCreate = User::create([
                    'company_id'     => $masterEmployee->company_id,
                    'employee_id'    => $masterEmployee->employee_id,
                    'employee_name'  => Str::upper($masterEmployee->employee_name),
                    'email'          => Str::lower($masterEmployee->employee_email),
                ]);

                if ($userCreate) {
                    return response()->json([
                        'status' => false,
                        'key' => 'password',
                        'message' => 'Please reset your password first'
                    ], 401);
                }
                else {
                    return response()->json([
                        'status' => false,
                        'key' => 'user_name',
                        'message' => 'Username or email not registered'
                    ], 401);
                }
            }
            else {
                return response()->json([
                    'status' => false,
                    'key' => 'user_name',
                    'message' => 'Username or email not registered'
                ], 401);
            }
        }

        // return back()->withErrors(['login_error' => 'Invalid credentials']);
        // return response()->json([
        //     'status' => false,
        //     'message' => 'Invalid login details'
        // ], 401);
    }

    private function createAvatar($params) {
        $employeeName = $params['employeeName'];
        $employeeId = $params['employeeId'];
        $manager = $image = new ImageManager(new Driver());

        $width = 60;
        $height = 60;
        $backgroundColor = '#115fad';
        $textColor = '#ffffff';
        $words = explode(' ', trim($employeeName));
        $initials = strtoupper(Str::substr($words[0], 0, 1));
        if (count($words) > 1) {
            $initials .= strtoupper(Str::substr($words[1], 0, 1));
        }

        $image = $manager->create($width, $height);
        $image->fill($backgroundColor);

        $fontSize = 24;
        $fontPath = public_path('css/app/fonts/Inter/Inter-Bold.ttf');

        $image->text($initials, $width / 2, $height / 2, function ($font) use ($fontPath, $fontSize, $textColor) {
            $font->file($fontPath);
            $font->size($fontSize);
            $font->color($textColor);
            $font->align('center');
            $font->valign('middle');
        });

        $filename = $employeeId.'_'.uniqid().'.webp';
        $path = "public/avatars/{$filename}";
        Storage::put($path, (string) $image->encode(new WebpEncoder(quality: 100)));

        DB::table('master_employees_avatar')->insert([
                'employee_id' => $employeeId,
                'avatar' => $filename,
                'is_active' => '1'
            ]);
    }

    public function registerForm() {
        return view('auth.register');
    }

    public function register(Request $request) {
        $request->validate([
            'user_nik' => 'required|string',
            'user_name' => 'required|string|max:255|unique:users',
            'email' => 'required|string|max:255|unique:users',
            'password' => 'required|string',
            'user_fullname' => 'required|string'
        ]);

        $user = new User;
        $user->employee_id = $request->user_nik;
        $user->user_name = $request->user_name;
        $user->employee_name = $request->user_fullname;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('login');
    }

    public function logout(Request $request) {
        // $request->session()->forget(['token', 'userNik', 'userName', 'url.intended']);
        // $request->session()->flush();
        // return redirect()->route('login');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->session()->flush();

        return redirect()->route('login');
    }

    public function changePassword(Request $request) {
        $decodeToken = SafeToken::decode($request->token);
        $resetId = $decodeToken['resetId'];
        $valid = false;
        $getReset = DB::table('password_reset')
                    ->select('employee_id', 'email', 'created_at', 'expired_at')
                    ->where('reset_id', $resetId)
                    ->where('is_active', '1')
                    ->first();

        if ($getReset) {
            $employeeId = $getReset->employee_id;
            $createdAt = $getReset->created_at;
            $expiredAt = $getReset->expired_at;

            if(Carbon::parse($getReset->expired_at)->format('Y-m-d H:i:s') > Carbon::now()->format('Y-m-d H:i:s')) {
                $valid = true;
            }
        }

        $token = $request->token;
        return view('auth.change_password', compact('token', 'valid'));

        // $employeeId = $decodeToken['id'];
        // $startDatetime = Carbon::parse($decodeToken['createdAt']);

        // if ($startDatetime->diffInMinutes(now()) <= 60) {
        //     $token = $request->token;
        //     return view('auth.change_password', compact('token'));
        // }
        // else {
        //     // LINK HAS BEEN EXPIRED
        // }
    }

    public function updatePassword(Request $request) {
        try {
            $validatedData = $request->validate([
                'token' => 'required|string',
                'newPassword' => 'required|string|max:255',
                'newPasswordConfirm' => 'required|string|max:255'
            ], [
                'token.required' => 'Token is required',
                'newPassword.required' => 'New Password is required',
                'newPasswordConfirm.required' => 'Confirm new password is required'
            ]);
        }
        catch (ValidationException $e) {
            return response()->json(['message' => 'Please fill the required form.', 'errors' => $e->errors()], 422)
                            ->setStatusCode(422, 'Please fill the required form.');
        }

        $token = SafeToken::decode($request->token);
        $newPassword = $request->newPassword;
        $newPasswordConfirm = $request->newPasswordConfirm;
        if($newPassword != $newPasswordConfirm) {
            return response()->json(['message' => 'Confirmation password does not match'],
                                     423)
            ->setStatusCode(423, 'Confirmation password does not match');
        }

        $valid = false;
        $employeeId = $token['id'];
        if($request->type == 'FORGOT') {
            $resetId = $token['resetId'];
            $getReset = DB::table('password_reset')
                        ->select('employee_id', 'email', 'created_at', 'expired_at')
                        ->where('reset_id', $resetId)
                        ->where('is_active', '1')
                        ->first();

            if ($getReset) {
                $employeeId = $getReset->employee_id;
                $createdAt = $getReset->created_at;
                $expiredAt = $getReset->expired_at;

                if(Carbon::parse($getReset->expired_at)->format('Y-m-d H:i:s') > Carbon::now()->format('Y-m-d H:i:s')) {
                    $valid = true;
                }
            }
        }
        else {
            $valid = true;
        }

        if($valid == true) {
            $newPassword = Hash::make($newPassword);
            $user = new User;
            $dataUpdate = [
                'password' => $newPassword,
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'remember_token' => null,
                'login_attempts' => null,
                'lockout_time' => null,
            ];

            $user->where('employee_id', $employeeId)
                ->where('is_active', 1)
                ->update($dataUpdate);

            if($request->type == 'FORGOT') {
                $resetId = $token['resetId'];
                $dataUpdate = [
                    'is_active' => '0',
                ];

                DB::table('password_reset')
                    ->where('employee_id', $employeeId)
                    ->where('is_active', '1')
                    ->update($dataUpdate);
            }

            $getUser = DB::table('users')
                        ->select('id')
                        ->where('employee_id', $employeeId)
                        ->where('is_active', '1')
                        ->first();
            DB::table('sessions')->where('user_id', $getUser->id)->delete();

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            $request->session()->flush();

            $redirect = route('login');
            $message = 'Successfully changed password';
            return response()->json(['message' => $message, 'redirect' => $redirect], 200);
        }
        else {
            $message = 'Password reset link has expired, please create forgot password again.';
            return response()->json(['message' => $message], 404);
        }
    }

    public function resetPassword(Request $request) {
        try {
            $validatedData = $request->validate([
                'email' => 'required|email'
            ], [
                'email.required' => 'Your email is required'
            ]);

            $getUser = MasterEmployees::select('employee_id', 'employee_name', 'employee_email', 'company_id')
                        ->where('employee_email', $request->email)
                        ->where('is_active', '1')
                        ->orderBy('id', 'asc')
                        ->first();
            if($getUser) {
                $createdAt = Carbon::now()->format('Y-m-d H:i:s');
                $expiredAt = Carbon::now()->addHour()->format('Y-m-d H:i:s');

                $dataInsert = [
                    'email' => $request->email,
                    'employee_id' => $getUser->employee_id,
                    'created_at' => $createdAt,
                    'expired_at' => $expiredAt,
                    'is_active' => '1',
                ];
                $insertReset = DB::table('password_reset')->insertGetId($dataInsert);

                $getUserCredential = User::select('id')
                                            ->where('employee_id', $getUser->employee_id)
                                            ->where('is_active', '1')
                                            ->first();
                if($getUserCredential) {
                    DB::table('sessions')->where('user_id', $getUserCredential->id)->delete();
                }
                else {
                    User::create([
                        'company_id'     => $getUser->company_id,
                        'employee_id'    => $getUser->employee_id,
                        'employee_name'  => Str::upper($getUser->employee_name),
                        'email'          => Str::lower($getUser->employee_email),
                    ]);
                }

                $token = ['resetId' => $insertReset, 'id' => $getUser->employee_id];
                $token = SafeToken::encode($token);

                $accountId = '3';
                $to = $request->email;
                $subject = 'Password reset - BML App';
                $body = '<table style="border-collapse:collapse;border:0;width:100%;font-size:13px;line-height:1.7;">
                            <tr>
                                <th colspan="4" style="font-weight:normal;padding:10px 5px 20px 5px;text-align:left">Dear '.Str::upper($getUser->employee_name).', <br/><br/>We got a request to reset your BML App password. Please click the Reset Password button below to input a new password, link is only valid for 1 hour.</th>
                            </tr>
                        </table>';

                $body .= '<table style="border-collapse: collapse; border: 0; width: 80%; margin : 40px auto 40px auto;">
                                <tr>
                                    <td valign="middle" style="text-align:center">
                                    <!--[if mso]>
                                        <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="'.url('/change_password?token='.$token).'" style="height:40px; v-text-anchor:middle; width: 270px;" arcsize="1" stroke="f" fillcolor="#115fad">
                                            <w:anchorlock/>
                                            <center style="color:#ebebeb; font-family:sans-serif; font-size:13px; font-weight:bold; text-decoration: none">Reset Password</center>
                                        </v:roundrect>
                                    <![endif]-->
                                    <!--[if !mso]> <!-->
                                        <a href="'.url('/change_password?token='.$token).'" target="_blank" style="font-size: 13px; border-radius: 10px; color: #ebebeb; background-color: #115fad; font-weight: bold; font-family:sans-serif; text-decoration: none; color: #ebebeb; padding: 8px 15px 8px 15px;">Reset Password</a>
                                    <!-- <![endif]-->
                                    </td>
                                </tr>
                            </table>';
                $body .= "<div style='font-size:13px; margin-top:7px; margin-bottom:20px; display:block; font-style:italic'>This email was generated automatically by system, please don't reply this email.</div>";

                $emailAccount = MasterEmailAccount::findOrFail($accountId);

                // Create transport and mailer
                $transport = new EsmtpTransport($emailAccount->smtp_host, $emailAccount->smtp_port, $emailAccount->smtp_encryption);
                $transport->setUsername($emailAccount->username);
                $transport->setPassword($emailAccount->password);

                $mailer = new Mailer($transport);

                // Create the email
                $email = (new Email())
                    ->from(new Address($emailAccount->username, $emailAccount->display_name)) // Set email and display name correctly
                    ->to($to)
                    ->subject($subject)
                    ->html($body);

                try {
                    // Send the email
                    $mailer->send($email);
                    return response()->json(['message' => 'Password Reset link successfully sent to your email'], 200);
                }
                catch (TransportExceptionInterface $e) {
                    return response()->json(['message' => 'Failed to send email', 'error' => $e->getMessage()], 500);
                }
            }
            else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Email not found',
                ], 404);
            }
        }
        catch (ValidationException $e) {
            return response()->json(['message' => 'Please fill the required form.', 'errors' => $e->errors()], 422)
                            ->setStatusCode(422, 'Please fill the required form.');
        }
    }

    public function alpine(Request $request)
    {
        // if ($request->session()->has('token')) {
        //     $decrypted = Crypt::decryptString($request->session()->get('token'));
        //     $token = json_decode($decrypted, true);
        //     if (isset($token['userNik']) && isset($token['userName'])) {
        //         return redirect()->route('home');
        //     }
        // }


        return view('alpine');
    }
}
