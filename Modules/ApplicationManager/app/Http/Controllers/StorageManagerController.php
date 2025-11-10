<?php

namespace Modules\ApplicationManager\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\RoleController;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

class StorageManagerController extends BaseController
{
    public function index(Request $request)
    {
        $employeeId = $this->employeeId;
        $employeeIdEncrypt = $this->employeeIdEncrypt;
        $companyId = $this->companyId;
        $employeeName = $this->employeeName;
        $roleId = $this->roleId;

        $roleController = new RoleController();
        $requestData = new Request();
        $requestData->replace([
                                'employeeId' => $employeeId,
                                'roleId' => $roleId,
                            ]);
        $getNavigation = $roleController->navigation($requestData);
        $navMenu = $getNavigation['navMenu'];
        $navSubmenu = $getNavigation['navSubmenu'];

        return view('applicationmanager::storage_manager', compact('employeeId', 'navMenu', 'navSubmenu', 'companyId', 'employeeIdEncrypt', 'employeeName'));
    }

    public function browse(Request $request)
    {
        $path = $request->get('path', '');
        $sort = $request->get('sort', 'name');
        $direction = $request->get('direction', 'asc');

        $fullPath = storage_path() . ($path ? '/' . $path : '');

        if (!file_exists($fullPath) || !is_dir($fullPath)) {
            return response()->json(['error' => 'Directory not found'], 404);
        }

        $items = [];
        $files = scandir($fullPath);

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;

            $itemPath = $fullPath . '/' . $file;
            $relativePath = $path ? $path . '/' . $file : $file;

            $isDir = is_dir($itemPath);
            $size = $isDir ? $this->getDirSize($itemPath) : filesize($itemPath);
            $created = Carbon::createFromTimestamp(filectime($itemPath));
            $modified = Carbon::createFromTimestamp(filemtime($itemPath));

            $items[] = [
                'name' => $file,
                'path' => $relativePath,
                'type' => $isDir ? 'folder' : 'file',
                'size' => $size,
                'size_human' => $this->formatBytes($size),
                'created_at' => $created->format('Y-m-d H:i:s'),
                'modified_at' => $modified->format('Y-m-d H:i:s'),
                'created_timestamp' => $created->timestamp,
                'modified_timestamp' => $modified->timestamp,
                'extension' => $isDir ? null : pathinfo($file, PATHINFO_EXTENSION),
                'icon' => $this->getFileIcon($file, $isDir)
            ];
        }

        // Sort items
        usort($items, function ($a, $b) use ($sort, $direction) {
            // Folders first
            if ($a['type'] !== $b['type']) {
                return $a['type'] === 'folder' ? -1 : 1;
            }

            $result = 0;
            switch ($sort) {
                case 'size':
                    $result = $a['size'] <=> $b['size'];
                    break;
                case 'created_at':
                    $result = $a['created_timestamp'] <=> $b['created_timestamp'];
                    break;
                case 'modified_at':
                    $result = $a['modified_timestamp'] <=> $b['modified_timestamp'];
                    break;
                default:
                    $result = strcasecmp($a['name'], $b['name']);
            }

            return $direction === 'desc' ? -$result : $result;
        });

        return response()->json([
            'items' => $items,
            'current_path' => $path,
            'breadcrumbs' => $this->getBreadcrumbs($path)
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'files.*' => 'required|file|max:102400', // 100MB max
            'path' => 'nullable|string'
        ]);

        $path = $request->get('path', '');
        $uploadPath = storage_path() . ($path ? '/' . $path : '');

        if (!file_exists($uploadPath)) {
            return response()->json(['error' => 'Directory not found'], 404);
        }

        $uploadedFiles = [];

        foreach ($request->file('files') as $file) {
            $filename = $file->getClientOriginalName();
            $destinationPath = $uploadPath . '/' . $filename;

            // Handle duplicate names
            $counter = 1;
            $originalName = pathinfo($filename, PATHINFO_FILENAME);
            $extension = pathinfo($filename, PATHINFO_EXTENSION);

            while (file_exists($destinationPath)) {
                $filename = $originalName . '_' . $counter . ($extension ? '.' . $extension : '');
                $destinationPath = $uploadPath . '/' . $filename;
                $counter++;
            }

            $file->move($uploadPath, $filename);
            $uploadedFiles[] = $filename;
        }

        return response()->json([
            'success' => true,
            'uploaded_files' => $uploadedFiles
        ]);
    }

    public function createFolder(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'path' => 'nullable|string'
        ]);

        $path = $request->get('path', '');
        $name = $request->get('name');
        $fullPath = storage_path() . ($path ? '/' . $path : '') . '/' . $name;

        if (file_exists($fullPath)) {
            return response()->json(['error' => 'Folder already exists'], 400);
        }

        if (mkdir($fullPath, 0755, true)) {
            return response()->json(['success' => true]);
        }

        return response()->json(['error' => 'Failed to create folder'], 500);
    }

    public function rename(Request $request)
    {
        $request->validate([
            'old_name' => 'required|string',
            'new_name' => 'required|string|max:255',
            'path' => 'nullable|string'
        ]);

        $path = $request->get('path', '');
        $oldName = $request->get('old_name');
        $newName = $request->get('new_name');

        $basePath = storage_path() . ($path ? '/' . $path : '');
        $oldPath = $basePath . '/' . $oldName;
        $newPath = $basePath . '/' . $newName;

        if (!file_exists($oldPath)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        if (file_exists($newPath)) {
            return response()->json(['error' => 'Name already exists'], 400);
        }

        if (rename($oldPath, $newPath)) {
            return response()->json(['success' => true]);
        }

        return response()->json(['error' => 'Failed to rename'], 500);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'path' => 'nullable|string'
        ]);

        $path = $request->get('path', '');
        $items = $request->get('items');
        $basePath = storage_path() . ($path ? '/' . $path : '');

        foreach ($items as $item) {
            $itemPath = $basePath . '/' . $item;

            if (!file_exists($itemPath)) {
                continue;
            }

            if (is_dir($itemPath)) {
                $this->deleteDirectory($itemPath);
            } else {
                unlink($itemPath);
            }
        }

        return response()->json(['success' => true]);
    }

    // public function download(Request $request)
    // {
    //     $path = $request->get('path', '');
    //     $filename = $request->get('filename');
    //     $filePath = storage_path() . ($path ? '/' . $path : '') . '/' . $filename;

    //     if (!file_exists($filePath) || is_dir($filePath)) {
    //         abort(404);
    //     }
    //     return response()->download($filePath);
    // }

    // public function downloadZip(Request $request)
    // {
    //     $path = $request->get('path', '');
    //     $items = $request->get('items', []);

    //     if (empty($items)) {
    //         return response()->json(['error' => 'No items selected'], 400);
    //     }

    //     $zipName = 'download_' . time() . '.zip';
    //     $zipPath = storage_path('app/temp/' . $zipName);

    //     // Create temp directory if not exists
    //     $tempDir = dirname($zipPath);
    //     if (!file_exists($tempDir)) {
    //         mkdir($tempDir, 0755, true);
    //     }

    //     $zip = new ZipArchive();
    //     if ($zip->open($zipPath, ZipArchive::CREATE) !== TRUE) {
    //         return response()->json(['error' => 'Cannot create zip file'], 500);
    //     }

    //     $basePath = storage_path() . ($path ? '/' . $path : '');

    //     dd($items);

    //     foreach ($items as $item) {
    //         $itemPath = $basePath . '/' . $item;

    //         if (!file_exists($itemPath)) {
    //             continue;
    //         }

    //         if (is_dir($itemPath)) {
    //             $this->addDirectoryToZip($zip, $itemPath, $item);
    //         } else {
    //             $zip->addFile($itemPath, $item);
    //         }
    //     }

    //     $zip->close();

    //     return response()->download($zipPath)->deleteFileAfterSend();
    // }

    public function download(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
            'filename' => 'required|string',
        ]);

        $path = $request->input('path');
        $filename = $request->input('filename');

        // Security: sanitize path dan filename
        $safePath = str_replace(['../', '..\\'], '', $path);
        $safeFilename = basename($filename);

        $filePath = storage_path() . ($safePath ? '/' . $safePath : '') . '/' . $safeFilename;

        if (!file_exists($filePath) || is_dir($filePath)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        $fileSize = filesize($filePath);

        // Untuk file besar (>10MB), gunakan streaming
        if ($fileSize > 10 * 1024 * 1024) {
            return $this->streamFile($filePath, $safeFilename);
        }

        // Untuk file kecil, download biasa
        return response()->download($filePath, $safeFilename);
    }

    public function downloadZip(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
            'items' => 'required|array',
            'items.*' => 'string',
        ]);

        $path = $request->input('path');
        $items = $request->input('items');

        if (empty($items)) {
            return response()->json(['error' => 'No items selected'], 400);
        }

        // Limit jumlah items untuk mencegah abuse
        if (count($items) > 100) {
            return response()->json(['error' => 'Too many items selected (max 100)'], 400);
        }

        $safePath = str_replace(['../', '..\\'], '', $path);
        $basePath = storage_path() . ($safePath ? '/' . $safePath : '');

        $zipName = 'download_' . time() . '.zip';
        $zipPath = storage_path('app/temp/' . $zipName);

        // Create temp directory if not exists
        $tempDir = dirname($zipPath);
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE) !== TRUE) {
            return response()->json(['error' => 'Cannot create zip file'], 500);
        }

        $addedFiles = 0;

        foreach ($items as $item) {
            $safeItem = basename($item); // Security: prevent directory traversal
            $itemPath = $basePath . '/' . $safeItem;

            if (!file_exists($itemPath)) {
                continue;
            }

            try {
                if (is_dir($itemPath)) {
                    $this->addDirectoryToZip($zip, $itemPath, $safeItem);
                    $addedFiles++;
                } else {
                    $zip->addFile($itemPath, $safeItem);
                    $addedFiles++;
                }
            } catch (Exception $e) {
                // Log error tapi lanjutkan dengan file lain
                Log::error("Error adding {$safeItem} to ZIP: " . $e->getMessage());
            }
        }

        $zip->close();

        if ($addedFiles === 0) {
            if (file_exists($zipPath)) {
                unlink($zipPath);
            }
            return response()->json(['error' => 'No files could be added to ZIP'], 400);
        }

        // Untuk ZIP besar, gunakan streaming
        $zipSize = filesize($zipPath);
        if ($zipSize > 50 * 1024 * 1024) { // 50MB
            return $this->streamFile($zipPath, $zipName, true);
        }

        return response()->download($zipPath, $zipName)->deleteFileAfterSend();
    }

    private function streamFile($filePath, $filename, $deleteAfter = false)
    {
        // Stream file untuk download besar
        $fileSize = filesize($filePath);
        $mimeType = mime_content_type($filePath);

        return response()->stream(function() use ($filePath, $deleteAfter) {
            $handle = fopen($filePath, 'rb');

            if ($handle === false) {
                return;
            }

            // Stream dalam chunks 8KB
            while (!feof($handle)) {
                echo fread($handle, 8192);
                flush();

                // Check jika client disconnect
                if (connection_aborted()) {
                    break;
                }
            }

            fclose($handle);

            // Delete file setelah streaming jika diminta
            if ($deleteAfter && file_exists($filePath)) {
                unlink($filePath);
            }
        }, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Length' => $fileSize,
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no', // Disable nginx buffering
        ]);
    }

    private function addDirectoryToZip($zip, $dirPath, $zipPath)
    {
        // Add directory to ZIP recursively
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dirPath),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = $zipPath . '/' . substr($filePath, strlen($dirPath) + 1);

                // Normalize path separators
                $relativePath = str_replace('\\', '/', $relativePath);

                $zip->addFile($filePath, $relativePath);
            }
        }
    }

    public function move(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'source_path' => 'nullable|string',
            'destination_path' => 'required|string'
        ]);

        $items = $request->get('items');
        $sourcePath = $request->get('source_path', '');
        $destinationPath = $request->get('destination_path');

        // Construct full paths
        $sourceBasePath = storage_path() . ($sourcePath ? '/' . ltrim($sourcePath, '/') : '');
        $destBasePath = storage_path() . '/' . ltrim($destinationPath, '/');

        // Debug logging (optional - remove in production)
        // \Log::info('Move operation:', [
        //     'source_path' => $sourcePath,
        //     'destination_path' => $destinationPath,
        //     'source_base_path' => $sourceBasePath,
        //     'dest_base_path' => $destBasePath,
        //     'items' => $items
        // ]);

        // Check if destination directory exists
        if (!file_exists($destBasePath)) {
            return response()->json([
                'error' => 'Destination directory not found: ' . $destBasePath,
                'destination_path' => $destinationPath
            ], 404);
        }

        if (!is_dir($destBasePath)) {
            return response()->json([
                'error' => 'Destination is not a directory: ' . $destBasePath
            ], 400);
        }

        $moved = [];
        $failed = [];

        foreach ($items as $item) {
            try {
                $sourceItemPath = $sourceBasePath . '/' . $item;
                $destItemPath = $destBasePath . '/' . $item;

                // Check if source exists
                if (!file_exists($sourceItemPath)) {
                    $failed[] = [
                        'item' => $item,
                        'error' => 'Source file not found: ' . $sourceItemPath
                    ];
                    continue;
                }

                // Check if destination already exists
                if (file_exists($destItemPath)) {
                    $failed[] = [
                        'item' => $item,
                        'error' => 'Destination already exists: ' . basename($destItemPath)
                    ];
                    continue;
                }

                // Perform the move
                if (rename($sourceItemPath, $destItemPath)) {
                    $moved[] = $item;
                } else {
                    $failed[] = [
                        'item' => $item,
                        'error' => 'Failed to move item'
                    ];
                }
            } catch (\Exception $e) {
                $failed[] = [
                    'item' => $item,
                    'error' => $e->getMessage()
                ];
            }
        }

        // Return response
        if (count($moved) > 0) {
            $message = count($moved) . ' item(s) moved successfully';
            if (count($failed) > 0) {
                $message .= ', ' . count($failed) . ' item(s) failed';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'moved' => $moved,
                'failed' => $failed
            ]);
        }
        else {
            dd([
                    'source_path' => $sourcePath,
            'destination_path' => $destinationPath,
            'source_base_path' => $sourceBasePath,
            'dest_base_path' => $destBasePath,
                'items' => $items,
                'failed' => $failed,
                'moved' => $moved
            ]);

            return response()->json([
                'success' => false,
                'error' => 'No items were moved',
                'failed' => $failed
            ], 400);
        }
    }

    public function duplicate(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'path' => 'nullable|string'
        ]);

        $items = $request->get('items');
        $path = $request->get('path', '');
        $basePath = storage_path() . ($path ? '/' . $path : '');

        foreach ($items as $item) {
            $sourcePath = $basePath . '/' . $item;

            if (!file_exists($sourcePath)) {
                continue;
            }

            $counter = 1;
            $pathInfo = pathinfo($item);
            $filename = $pathInfo['filename'];
            $extension = isset($pathInfo['extension']) ? '.' . $pathInfo['extension'] : '';

            do {
                $newName = $filename . '_copy' . $counter . $extension;
                $destPath = $basePath . '/' . $newName;
                $counter++;
            } while (file_exists($destPath));

            if (is_dir($sourcePath)) {
                $this->copyDirectory($sourcePath, $destPath);
            } else {
                copy($sourcePath, $destPath);
            }
        }

        return response()->json(['success' => true]);
    }

    private function getDirSize($directory)
    {
        $size = 0;
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory)) as $file) {
            $size += $file->getSize();
        }
        return $size;
    }

    private function formatBytes($size, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }

        return round($size, $precision) . ' ' . $units[$i];
    }

    private function getFileIcon($filename, $isDir)
    {
        if ($isDir) {
            return 'fa-solid fa-folder folder-table';
        }

        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        $iconMap = [
            'pdf' => 'fa-regular fa-file-pdf',
            'doc' => 'fa-regular fa-file-doc',
            'docx' => 'fa-regular fa-file-doc',
            'xls' => 'fa-regular fa-file-xls',
            'xlsx' => 'fa-regular fa-file-xls',
            'ppt' => 'fa-regular fa-file-ppt',
            'pptx' => 'fa-regular fa-file-ppt',
            'webp' => 'fa-regular fa-file-image',
            'jpg' => 'fa-regular fa-file-jpg',
            'jpeg' => 'fa-regular fa-file-jpg',
            'png' => 'fa-regular fa-file-png',
            'gif' => 'fa-regular fa-file-gif',
            'svg' => 'fa-regular fa-file-svg',
            'mp4' => 'fa-regular fa-file-mp4',
            'avi' => 'fa-regular fa-file-video',
            'mov' => 'fa-regular fa-file-mov',
            'mp3' => 'fa-regular fa-file-mp3',
            'wav' => 'fa-regular fa-file-audio',
            'zip' => 'fa-regular fa-file-zip',
            'rar' => 'fa-regular fa-file-zipper',
            '7z' => 'fa-regular fa-file-zipper',
            'txt' => 'fa-regular fa-file-lines',
            'php' => 'fa-regular fa-file-code',
            'js' => 'fa-regular fa-file-code',
            'css' => 'fa-regular fa-file-code',
            'html' => 'fa-regular fa-file-code',
        ];

        return $iconMap[$extension] ?? 'fa-regular fa-file-lines';
    }

    private function getBreadcrumbs($path)
    {
        if (empty($path)) {
            return [['name' => 'Storage', 'path' => '']];
        }

        $parts = explode('/', $path);
        $breadcrumbs = [['name' => 'Storage', 'path' => '']];
        $current = '';

        foreach ($parts as $part) {
            $current .= ($current ? '/' : '') . $part;
            $breadcrumbs[] = ['name' => $part, 'path' => $current];
        }

        return $breadcrumbs;
    }

    private function deleteDirectory($dir)
    {
        if (!file_exists($dir)) return;

        $files = array_diff(scandir($dir), ['.', '..']);

        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }

        rmdir($dir);
    }

    public function getFolderTree(Request $request)
    {
        $path = $request->get('path', '');

        // Sanitize path
        // $path = trim($path, '/');

        $fullPath = storage_path() . ($path ? '/' . $path : '');

        if (!file_exists($fullPath) || !is_dir($fullPath)) {
            return response()->json(['error' => 'Directory not found'], 404);
        }

        $folders = [];
        $files = scandir($fullPath);

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;

            $itemPath = $fullPath . '/' . $file;

            // Only return folders for tree view
            if (is_dir($itemPath)) {
                $relativePath = $path ? $path . '/' . $file : $file;

                // Check if folder has subfolders
                $hasSubfolders = $this->hasSubfolders($itemPath);

                $folders[] = [
                    'name' => $file,
                    'path' => $relativePath,
                    'type' => 'folder',
                    'has_children' => $hasSubfolders,
                    'created_at' => Carbon::createFromTimestamp(filectime($itemPath))->format('Y-m-d H:i:s'),
                    'modified_at' => Carbon::createFromTimestamp(filemtime($itemPath))->format('Y-m-d H:i:s'),
                ];
            }
        }

        // Sort folders alphabetically
        usort($folders, function ($a, $b) {
            return strcasecmp($a['name'], $b['name']);
        });

        return response()->json([
            'success' => true,
            'folders' => $folders,
            'current_path' => $path,
            'parent_path' => $this->getParentPath($path)
        ]);
    }

    private function hasSubfolders($path)
    {
        if (!is_dir($path) || !is_readable($path)) {
            return false;
        }

        $files = scandir($path);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;

            if (is_dir($path . '/' . $file)) {
                return true;
            }
        }

        return false;
    }

    private function getParentPath($path)
    {
        if (empty($path)) {
            return null;
        }

        $parts = explode('/', $path);
        array_pop($parts);

        return implode('/', $parts);
    }
}
