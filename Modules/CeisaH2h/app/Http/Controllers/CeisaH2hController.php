<?php

namespace Modules\CeisaH2h\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Modules\CeisaH2h\Models\CeisaH2hModel;

class CeisaH2hController extends Controller
{
    public function getCeisaUser()
    {
        $ceisa = new CeisaH2hModel();
        $getData = $ceisa->masterCeisaH2hUser()->select('user_id', 'username', 'password')->where('is_active', '=', '1')->first();
        return $getData;
    }

    public function getJenisImpor()
    {
        $ceisa = new CeisaH2hModel();
        $getData = $ceisa->masterCeisaJenisImpor()->select('jenis_impor_id', 'kode_jenis_impor', 'nama_jenis_impor')->where('is_active', '=', '1')->orderBy('kode_jenis_impor', 'asc')->get();
        return $getData;
    }

    public function getCaraBayar()
    {
        $ceisa = new CeisaH2hModel();
        $getData = $ceisa->masterCeisaCaraBayar()->select('cara_bayar_id', 'kode_cara_bayar', 'nama_cara_bayar')
                                                    ->where('bc20', '=', '1')
                                                    ->where('is_active', '=', '1')
                                                    ->orderBy('kode_cara_bayar', 'asc')->get();
        return $getData;
    }

    public function getJenisIdentitas()
    {
        $ceisa = new CeisaH2hModel();
        $getData = $ceisa->masterCeisaJenisIdentitas()->select('kode_jenis_identitas', 'nama_jenis_identitas')->where('is_active', '=', '1')->orderBy('kode_jenis_identitas', 'asc')->get();
        return $getData;
    }

    public function getJenisEntitas()
    {
        $ceisa = new CeisaH2hModel();
        $getData = $ceisa->masterCeisaJenisEntitas()->select('kode_jenis_entitas', 'nama_jenis_entitas')->where('is_active', '=', '1')->orderBy('kode_jenis_entitas', 'asc')->get();
        return $getData;
    }

    public function getJenisApiEntitas()
    {
        $ceisa = new CeisaH2hModel();
        $getData = $ceisa->masterCeisaJenisApiEntitas()->select('kode_jenis_api_entitas', 'nama_jenis_api_entitas')->where('is_active', '=', '1')->orderBy('kode_jenis_api_entitas', 'asc')->get();
        return $getData;
    }

    public function get_data_form(Request $request)
    {
        $ceisa = new CeisaH2hModel();
        $dataType = $request->dataType;
        $search = $request->search;
        $dataValue = $request->dataValue;
        // $getData = array();
        $data = array();
        if($dataType === 'kodePelabuhan'){
            $getData = $ceisa->masterCeisaPelabuhan()->select('kode_pelabuhan', 'nama_pelabuhan', 'kode_kantor')
                                                        ->whereNotNull('kode_kantor')
                                                        ->where(function($query) use ($search) {
                                                            $query->where('kode_pelabuhan', 'like', '%'.$search.'%')
                                                                  ->orWhere('nama_pelabuhan', 'like', '%'.$search.'%')
                                                                  ->orWhere('kode_kantor', 'like', '%'.$search.'%');
                                                        })
                                                        ->where('is_active', '=', 1)
                                                        ->orderBy('kode_pelabuhan', 'asc')
                                                        ->get();                                     
            foreach ($getData as $rowData) {
                $record['id'] = $rowData->kode_pelabuhan;
                $record['text'] = $rowData->kode_pelabuhan.' - '.$rowData->nama_pelabuhan;
                $data[] = $record;
            }
        }
        else if($dataType === 'getKantor'){  
            $getData = $ceisa->masterCeisaKantor()->from('master_ceisa_kantor as a')
                                                    ->select('a.kode_kantor', 'a.nama_kantor_pendek', 'a.nama_kantor_panjang', 'b.kode_pelabuhan')
                                                    ->leftJoin('master_ceisa_pelabuhan as b', 'a.kode_kantor', '=', 'b.kode_kantor')
                                                    ->where('b.kode_pelabuhan', $dataValue)
                                                    ->where('a.is_active', '=', 1)
                                                    ->get();
            foreach ($getData as $rowData) {
                $record['id'] = $rowData->kode_kantor;
                $namaKantor = ($rowData->nama_kantor_panjang == null || $rowData->nama_kantor_panjang == '') ? $rowData->nama_kantor_pendek : $rowData->nama_kantor_panjang;
                $record['text'] = $rowData->kode_kantor.' - '.$namaKantor;
                $data[] = $record;
            }
        }
        else if($dataType === '5'){
            $ceisaAuth = $this->ceisa_auth()->getData(true);
            $accessToken = $ceisaAuth['accessToken'];
            $dataValue = str_replace('.', '', $dataValue);
            $dataValue = str_replace('-', '', $dataValue);

            $client = new Client();
            $companyByNpwp = $client->get('https://apis-gw.beacukai.go.id/v2/sce-ws/profil/perusahaan/data-perusahaan-by-npwp/?npwp='.$dataValue, [
            // $responseCeisa = $client->get('https://apis-gw.beacukai.go.id/v2/sce-ws/profil/perusahaan/perusahaanib-by-npwp9?npwp9='.$dataValue, [
                'headers' => [
                    'Authorization' => 'Bearer '.$accessToken,
                ],
            ]);
            $responseBody = json_decode($companyByNpwp->getBody(), true);
            /* $companyByNpwp response
                {
                    "idPerusahaan": "081815",
                    "kodeId": "5",
                    "nib": "8120008762239",
                    "npwp": "013130737075000",
                    "namaPerusahaan": "TEKNINDOPURI AMPUHPERKASA",
                    "alamatPerusahaan": "JL.KREKOT JAYA BLOK A.2, 15-16,JAKARTA PUSAT",
                    "rtRw": "004/007",
                    "kelurahan": "-",
                    "daerahId": "3171021001",
                    "kodePos": "10710",
                    "nomorTelpon": "021-3863026",
                    "kategori": "H",
                    "flagCabutNib": null,
                    "nik": "6254",
                    "npppjk": null,
                    "kodeUsaha": "05",
                    "niper": null,
                    "nppbkc": null,
                    "nomorApi": "090203418-P",
                    "tanggalApi": "2016-06-09T17:00:00.000+00:00",
                    "instansiPenerbitApi": "DINAS PERDAGANGAN PROVINSI",
                    "penerbitApiLainnya": null,
                    "longitude": " 106.8286101            ",
                    "latitude": " -6.1599254",
                    "fasilitas": null
                }
            */

            $jenisApi = null;
            if($responseBody['nomorApi'] != null){
                list($no, $jenis) = explode('-', $responseBody['nomorApi']);               
                if($jenis){
                    if($jenis == 'U'){
                        $jenisApi = '01';
                    }
                    else if($jenis == 'P'){
                        $jenisApi = '02';
                    }
                }
            }

            $data['status'] = true;
            $data['identitas'] = array(
                                    'npwp' => $responseBody['npwp'],
                                    'namaPerusahaan' => $responseBody['namaPerusahaan'],
                                    'alamatPerusahaan' => $responseBody['alamatPerusahaan'],
                                    'nib' => $responseBody['nib'] == null ? '' : $responseBody['nib'],
                                    'jenisApi' => $jenisApi,
                                );
        }
        else if($dataType === 'namaNegara'){
            $getData = $ceisa->masterCeisaNegara()->select('kode_negara', 'nama_negara')
                                                        ->where(function($query) use ($search) {
                                                            $query->where('kode_negara', 'like', '%'.$search.'%')
                                                                  ->orWhere('nama_negara', 'like', '%'.$search.'%');
                                                        })
                                                        ->where('is_active', '=', 1)
                                                        ->orderBy('kode_negara', 'asc')
                                                        ->get();
            foreach ($getData as $rowData) {
                $record['id'] = $rowData->kode_negara;
                $record['text'] = $rowData->kode_negara.' - '.$rowData->nama_negara;
                $data[] = $record;
            }
        }

        return response()->json($data, 200);
    }

    protected function ceisaAuthLogin(){
        $getCeisaUser = $this->getCeisaUser();
        $client = new Client();
        try {
            $response = $client->post('https://apis-gw.beacukai.go.id/nle-oauth/v1/user/login', [
                'headers' => [
                    'Content-Type' => 'application/json', // Set Content-Type ke application/json
                    'Accept' => 'application/json',
                ],
                'body' => json_encode([
                    'username' => $getCeisaUser->username,
                    'password' => $getCeisaUser->password
                ])
            ]);
    
            $data = array();
            $data['user_id'] = $getCeisaUser->user_id;
            $data['response'] = json_decode($response->getBody(), true);
            $data['expires_at'] = Carbon::now()->addSeconds($data['response']['item']['expires_in']);
            return $data;
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                return $e->getResponse()->getBody()->getContents();
            }
            return $e->getMessage();
        }
    }

    protected function refreshAccessToken($refreshToken) {
        $getCeisaUser = $this->getCeisaUser();
        $client = new Client();
        try {
            $response = $client->post('https://apis-gw.beacukai.go.id/nle-oauth/v1/user/login', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'body' => json_encode([
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $refreshToken,
                    'username' => $getCeisaUser->username,
                    'password' => $getCeisaUser->password
                ])
            ]);

            $data = array();
            $data['user_id'] = $getCeisaUser->user_id;
            $data['response'] = json_decode($response->getBody(), true);
            $data['expires_at'] = Carbon::now()->addSeconds($data['response']['item']['expires_in']);
            return $data;
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                return response($e->getResponse()->getBody()->getContents(), $e->getResponse()->getStatusCode());
            }
            return response($e->getMessage(), 500);
        }
    }

    public function ceisa_auth($responseType = null){
        $getCeisaUser = $this->getCeisaUser();
        $response = array();
        $response['status'] = false;
        if($getCeisaUser){
            $CeisaH2hModel = new CeisaH2hModel();
            $getCeisaAuth = $CeisaH2hModel->masterCeisaH2hAuth()
                                        ->select('xuser_id', 'access_token', 'expires_in', 'refresh_token', 'token_type', 'id_token', 'expires_at', 'updated_at')
                                        ->where('user_id', '=', $getCeisaUser->user_id)
                                        ->orderBy('updated_at', 'desc')
                                        ->first();

            if (!$getCeisaAuth) {
                $ceisaAuthLogin = $this->ceisaAuthLogin();
                $userId = $ceisaAuthLogin['user_id'];
                $accessToken = $ceisaAuthLogin['response']['item']['access_token'];
                $expiresIn = $ceisaAuthLogin['response']['item']['expires_in'];
                $refreshToken = $ceisaAuthLogin['response']['item']['refresh_token'];
                $tokenType = $ceisaAuthLogin['response']['item']['token_type'];
                $idToken = $ceisaAuthLogin['response']['item']['id_token'];
                $sessionState = $ceisaAuthLogin['response']['item']['session_state'];
                $expiresAt = $ceisaAuthLogin['expires_at'];

                $CeisaH2hModel->masterCeisaH2hAuth()->insert([
                                                    'user_id' => $userId,
                                                    'access_token' => $accessToken,
                                                    'expires_in' => $expiresIn,
                                                    'refresh_token' => $refreshToken,
                                                    'token_type' => $tokenType,
                                                    'id_token' => $idToken,
                                                    'expires_at' => $expiresAt,
                                                    'updated_at' => Carbon::now(),
                                                ]);
            }
            else {
                $currentTime = Carbon::now();
                $accessToken = $getCeisaAuth->access_token;
                $expiresIn = $getCeisaAuth->expires_in;
                $refreshToken = $getCeisaAuth->refresh_token;
                $tokenType = $getCeisaAuth->token_type;
                $expiresAt = Carbon::parse($getCeisaAuth->expires_at);

                if ($expiresAt->lessThan($currentTime)){
                    // REFRESH TOKEN
                    $newTokens = $this->refreshAccessToken($refreshToken);
                    $accessToken = $newTokens['response']['item']['access_token'];
                    $expiresIn = $newTokens['response']['item']['expires_in'];
                    $tokenType = $newTokens['response']['item']['token_type'];
                    $refreshToken = $newTokens['response']['item']['refresh_token'];
                    $expiresAt = $newTokens['expires_at'];

                    $CeisaH2hModel->masterCeisaH2hAuth()
                                ->where('user_id', $getCeisaAuth->user_id)
                                ->update([
                                    'access_token' => $accessToken,
                                    'expires_in' => $expiresIn,
                                    'refresh_token' => $refreshToken,
                                    'expires_at' => $expiresAt,
                                    'updated_at' => Carbon::now(),
                                ]);
                }
            }    

            // $response['status'] = true;
            // $response['accessToken'] = $accessToken;
            // $response['authType'] = $tokenType;

            return response()->json([
                'status' => true,
                'accessToken' => $accessToken,
                'authType' => $tokenType,
                'message' => 'Successfully connected to CEISA 4.0 gateway'
            ], 200);
        }
        else{
            return response()->json([
                'status' => false,
                'message' => 'Account portal CEISA 4.0 not found'
            ], 401);
        }

        // if($responseType == null){
        //     echo json_encode($response);
        // }
        // else{
        //     return $response;
        // }

        
    }

    public function dokumen_pabean(Request $request){  
        // $this->ceisa_auth();     
        return view('ceisah2h::doc_pabean');
    }

    public function bc20(Request $request){
        $user = Auth::user();
        $employeeId = $user->employee_id;
        $this->ceisa_auth();    
        $data = array();
        
        // $client = new Client();
        // $responsePpjk = $client->get('https://apis-gw.beacukai.go.id/v2/sce-ws/profil/perusahaan/perusahaanib-by-npwp9?npwp9=017710112062000', [
        //     'headers' => [
        //         'Authorization' => 'Bearer '.$accessToken,
        //     ],
        // ]);
        // $ppjk = json_decode($responsePpjk->getBody(), true);
        // $data['ppjk'] = array(
        //                         'npwpPerseroan' => $ppjk['npwpPerseroan'],
        //                         'nmNpwp' => 'PT. '.$ppjk['nmNpwp'],
        //                         'alNpwp' => $ppjk['alNpwp'],
        //                         'nib' => $ppjk['nib'],
        //                     );
        
        // dd($accessToken);
        // exit();    
        
        $getJenisImpor = $this->getJenisImpor();
        $getCaraBayar = $this->getCaraBayar();
        $getJenisIdentitas = $this->getJenisIdentitas();

        $getJenisApiEntitas = $this->getJenisApiEntitas();
        $getJenisEntitas = $this->getJenisEntitas();
        $jenisEntitas = array();
        foreach ($getJenisEntitas as $rowData) {
            $jenisEntitas[$rowData->nama_jenis_entitas] = $rowData->kode_jenis_entitas; 
        }       

        return view('ceisah2h::bc20', compact('getJenisImpor', 'getCaraBayar', 'getJenisIdentitas', 'getJenisApiEntitas', 'jenisEntitas', 'employeeId'));
    }
}
