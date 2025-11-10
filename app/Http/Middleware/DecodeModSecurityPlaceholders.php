<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DecodeModSecurityPlaceholders
{
    public function handle(Request $request, Closure $next): Response
    {
        $skipFields = ['csrf_token', '_token'];
        $keywordReplacements = [
            '_HEA_D_' => 'HEAD',
            '_Hea_d_' => 'Head',
            '_hea_d_' => 'head',
            '_FO_R_' => 'FOR',
            '_Fo_r_' => 'For',
            '_fo_r_' => 'for',
            '_REPLAC_E_' => 'REPLACE',
            '_Replac_e_' => 'Replace',
            '_replac_e_' => 'replace',
            '_UPDAT_E_' => 'UPDATE',
            '_Updat_e_' => 'Update',
            '_updat_e_' => 'update',
            '_ORDE_R_' => 'ORDER',
            '_Orde_r_' => 'Order',
            '_orde_r_' => 'order',
            '_GROU_P_' => 'GROUP',
            '_Grou_p_' => 'Group',
            '_grou_p_' => 'group',
            '_DOCUMEN_T_' => 'DOCUMENT',
            '_Documen_t_' => 'Document',
            '_documen_t_' => 'document',
            '_REQUIR_E_' => 'REQUIRE',
            '_Requir_e_' => 'Require',
            '_requir_e_' => 'require',
            '_IMPOR_T_' => 'IMPORT',
            '_Impor_t_' => 'Import',
            '_impor_t_' => 'import',
            '_USE_R_' => 'USER',
            '_Use_r_' => 'User',
            '_use_r_' => 'user',
            '_ADMI_N_' => 'ADMIN',
            '_Admi_n_' => 'Admin',
            '_admi_n_' => 'admin',
            '_SYSTE_M_' => 'SYSTEM',
            '_Syste_m_' => 'System',
            '_syste_m_' => 'system',
            '_DRO_P_' => 'DROP',
            '_Dro_p_' => 'Drop',
            '_dro_p_' => 'drop',
        ];

        $input = $request->all();
        array_walk_recursive($input, function (&$value, $key) use ($keywordReplacements, $skipFields) {
            if (is_string($value)) {
                // Skip CSRF token fields
                if (in_array($key, $skipFields)) {
                    return;
                }

                // First decode URL encoding
                $value = urldecode($value);

                // Replace special characters that were manually encoded
                $specialCharReplacements = [
                    '_SINGLE_QUOTE_' => "'",
                    '_DOUBLE_QUOTE_' => '"',
                    '_PAREN_OPEN_' => '(',
                    '_PAREN_CLOSE_' => ')',
                ];

                foreach ($specialCharReplacements as $encoded => $original) {
                    $value = str_replace($encoded, $original, $value);
                }

                // Replace keywords
                foreach ($keywordReplacements as $encoded => $original) {
                    $value = str_replace($encoded, $original, $value);
                }
            }
        });

        // Store decoded filenames in request attributes instead of file object
        $decodedFilenames = [];

        // Handle uploaded files - decode filenames
        if ($request->hasFile('files')) {
            $files = $request->file('files');

            if (is_array($files)) {
                foreach ($files as $index => $file) {
                    if ($file && method_exists($file, 'getClientOriginalName')) {
                        $originalName = $file->getClientOriginalName();
                        $decodedName = $this->decodeFilename($originalName, $keywordReplacements);

                        $decodedFilenames['files'][$index] = $decodedName;
                    }
                }
            } else {
                // Single file
                if ($files && method_exists($files, 'getClientOriginalName')) {
                    $originalName = $files->getClientOriginalName();
                    $decodedName = $this->decodeFilename($originalName, $keywordReplacements);

                    $decodedFilenames['files'] = $decodedName;
                }
            }
        }

        // Handle other file input names
        $fileInputNames = ['file', 'upload', 'attachment', 'document'];
        foreach ($fileInputNames as $inputName) {
            if ($request->hasFile($inputName)) {
                $file = $request->file($inputName);
                if ($file && method_exists($file, 'getClientOriginalName')) {
                    $originalName = $file->getClientOriginalName();
                    $decodedName = $this->decodeFilename($originalName, $keywordReplacements);

                    $decodedFilenames[$inputName] = $decodedName;
                }
            }
        }

        // Store decoded filenames in request attributes
        $request->attributes->set('decoded_filenames', $decodedFilenames);

        // Merge the processed input back to the request
        $request->merge($input);

        return $next($request);
    }

    private function decodeFilename(string $filename, array $keywordReplacements): string
    {
        $decoded = urldecode($filename);

        // Replace special characters that were manually encoded
        $specialCharReplacements = [
            '_SINGLE_QUOTE_' => "'",
            '_DOUBLE_QUOTE_' => '"',
            '_PAREN_OPEN_' => '(',
            '_PAREN_CLOSE_' => ')',
        ];

        foreach ($specialCharReplacements as $encoded => $original) {
            $decoded = str_replace($encoded, $original, $filename);
        }

        // Replace keywords
        foreach ($keywordReplacements as $encoded => $original) {
            $decoded = str_replace($encoded, $original, $decoded);
        }

        return $decoded;
    }

    public static function decodeFilenameStatic(string $filename): string
    {
        $keywordReplacements = [
            '_HEA_D_' => 'HEAD',
            '_Hea_d_' => 'Head',
            '_hea_d_' => 'head',
            '_FO_R_' => 'FOR',
            '_Fo_r_' => 'For',
            '_fo_r_' => 'for',
            '_REPLAC_E_' => 'REPLACE',
            '_Replac_e_' => 'Replace',
            '_replac_e_' => 'replace',
            '_UPDAT_E_' => 'UPDATE',
            '_Updat_e_' => 'Update',
            '_updat_e_' => 'update',
            '_ORDE_R_' => 'ORDER',
            '_Orde_r_' => 'Order',
            '_orde_r_' => 'order',
            '_GROU_P_' => 'GROUP',
            '_Grou_p_' => 'Group',
            '_grou_p_' => 'group',
            '_DOCUMEN_T_' => 'DOCUMENT',
            '_Documen_t_' => 'Document',
            '_documen_t_' => 'document',
            '_REQUIR_E_' => 'REQUIRE',
            '_Requir_e_' => 'Require',
            '_requir_e_' => 'require',
            '_IMPOR_T_' => 'IMPORT',
            '_Impor_t_' => 'Import',
            '_impor_t_' => 'import',
            '_USE_R_' => 'USER',
            '_Use_r_' => 'User',
            '_use_r_' => 'user',
            '_ADMI_N_' => 'ADMIN',
            '_Admi_n_' => 'Admin',
            '_admi_n_' => 'admin',
            '_SYSTE_M_' => 'SYSTEM',
            '_Syste_m_' => 'System',
            '_syste_m_' => 'system',
            '_DRO_P_' => 'DROP',
            '_Dro_p_' => 'Drop',
            '_dro_p_' => 'drop',
        ];

        // Replace special characters that were manually encoded BEFORE urldecode
        $specialCharReplacements = [
            '_SINGLE_QUOTE_' => "'",
            '_DOUBLE_QUOTE_' => '"',
            '_PAREN_OPEN_' => '(',
            '_PAREN_CLOSE_' => ')',
        ];

        $decoded = $filename;

        // Replace special characters first (before urldecode)
        foreach ($specialCharReplacements as $encoded => $original) {
            $decoded = str_replace($encoded, $original, $decoded);
        }

        // Then urldecode
        $decoded = urldecode($decoded);

        // Replace keywords
        foreach ($keywordReplacements as $encoded => $original) {
            $decoded = str_replace($encoded, $original, $decoded);
        }

        return $decoded;
    }
}