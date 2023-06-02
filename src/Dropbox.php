<?php

namespace Deskola\SimpleDropbox;

class Dropbox
{
    private $clientKey;
    private $clientSecrete;
    private $token;

    public function __construct($accessToken)
    {
        if (is_array($accessToken)) {
            [$this->clientKey, $this->clientSecrete] = $accessToken;
        }

        if (is_string($accessToken)) {
            $this->token = $accessToken;
        }
    }

    public function generate_refresh_token($code, $grantType = '')
    {
        $payload = [
            'code' => $code,
            'grant_type' => 'authorization_code'
        ];

        $header = [
            'Content-Type: application/x-www-form-urlencoded'
        ];


        $ch = curl_init('https://api.dropboxapi.com/oauth2/token');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERPWD, $this->clientKey . ":" . $this->clientSecrete);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        $apiResponse = curl_exec($ch);
        curl_close($ch);

        return is_null(json_decode($apiResponse, true))
            ? $apiResponse
            : json_decode($apiResponse, true);
    }

    public function refresh_token($refreshCode)
    {
        $payload = [
            'refresh_token' => $refreshCode,
            'grant_type' => 'refresh_token'
        ];

        $header = [
            'Content-Type: application/x-www-form-urlencoded'
        ];

        $ch = curl_init('https://api.dropboxapi.com/oauth2/token');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERPWD, $this->clientKey . ":" . $this->clientSecrete);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        $apiResponse = curl_exec($ch);
        curl_close($ch);

        return is_null(json_decode($apiResponse, true))
            ? $apiResponse
            : json_decode($apiResponse, true);
    }

    public function create_folder($path, $autorename = false)
    {
        $payload = [
            'path' => $path,
            'autorename' => $autorename,
        ];

        $header = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token
        ];

        $ch = curl_init('https://api.dropboxapi.com/2/files/create_folder_v2');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        $apiResponse = curl_exec($ch);
        curl_close($ch);

        return is_null(json_decode($apiResponse, true))
            ? $apiResponse
            : json_decode($apiResponse, true);

    }

    public function list_folders($path, $recursive = false, $include_media_info = false, $include_deleted = false,
                                 $include_has_explicit_shared_members = false, $include_mounted_folders = true, $include_non_downloadable_files = false)
    {
        $payload = [
            'path' => $path,
            'recursive' => $recursive,
            'include_media_info' => $include_media_info,
            'include_deleted' => $include_deleted,
            'include_has_explicit_shared_members' => $include_has_explicit_shared_members,
            'include_mounted_folders' => $include_mounted_folders,
            'include_non_downloadable_files' => $include_non_downloadable_files,
        ];

        $header = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token
        ];

        $ch = curl_init('https://api.dropboxapi.com/2/files/list_folder');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        $apiResponse = curl_exec($ch);
        curl_close($ch);

        return is_null(json_decode($apiResponse, true))
            ? $apiResponse
            : json_decode($apiResponse, true);

    }

    public function file_upload($path, $fileHandler, $size, $mode = "add", $autorename = false)
    {
        $headerArgs = json_encode([
            'path' => $path,
            'mode' => $mode,
            'autorename' => $autorename
        ]);

        $header = [
            'Content-Type: application/octet-stream',
            'Authorization: Bearer ' . $this->token,
            'Dropbox-API-Arg: ' . $headerArgs
        ];

        $ch = curl_init('https://content.dropboxapi.com/2/files/upload');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_PUT, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_INFILE, $fileHandler);
        curl_setopt($ch, CURLOPT_INFILESIZE, $size);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $apiResponse = curl_exec($ch);

        //echo $response;
        curl_close($ch);
        fclose($fileHandler);

        return is_null(json_decode($apiResponse, true))
            ? $apiResponse
            : json_decode($apiResponse, true);
    }

    public function download($path, $destination, $downloadFormat = 'normal')
    {
        $out_fp = fopen($destination, 'w+');
        if ($out_fp === FALSE) {
            echo "fopen error; can't open $destination\n";
            return (NULL);
        }

        $headerArgs = json_encode([
            'path' => $path
        ]);

        $header = [
            'Content-Type:',
            'Authorization: Bearer ' . $this->token,
            'Dropbox-API-Arg: ' . $headerArgs
        ];

        $url = $downloadFormat == 'zip'
            ? 'https://content.dropboxapi.com/2/files/download_zip'
            : 'https://content.dropboxapi.com/2/files/download';


        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_PUT, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FILE, $out_fp);

        $metadata = null;
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, function ($ch, $header) use (&$metadata) {
            $prefix = 'dropbox-api-result:';
            if (strtolower(substr($header, 0, strlen($prefix))) === $prefix) {
                $metadata = json_decode(substr($header, strlen($prefix)), true);
            }
            return strlen($header);
        }
        );

        curl_exec($ch);
        curl_close($ch);

        return $metadata;

    }

    public function delete($path)
    {
        $payload = [
            'path' => $path,
        ];

        $header = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token
        ];

        $ch = curl_init('https://api.dropboxapi.com/2/files/delete_v2');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        $apiResponse = curl_exec($ch);
        curl_close($ch);

        return is_null(json_decode($apiResponse, true))
            ? $apiResponse
            : json_decode($apiResponse, true);

    }

    public function file_search($searchWord, $include_highlights = false)
    {
        $payload = [
            'query' => $searchWord,
            'include_highlights' => $include_highlights
        ];

        $header = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token
        ];

        $ch = curl_init('https://api.dropboxapi.com/2/files/search_v2');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        $apiResponse = curl_exec($ch);
        curl_close($ch);

        return is_null(json_decode($apiResponse, true))
            ? $apiResponse
            : json_decode($apiResponse, true);

    }

    public function move($from_path, $to_path, $allow_shared_folder = false, $autorename = false,
                         $allow_ownership_transfer = false)
    {
        $payload = [
            'from_path' => $from_path,
            'to_path' => $to_path,
            'allow_shared_folder' => $allow_shared_folder,
            'autorename' => $autorename,
            'allow_ownership_transfer' => $allow_ownership_transfer
        ];

        $header = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token
        ];

        $ch = curl_init('https://api.dropboxapi.com/2/files/move_v2');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        $apiResponse = curl_exec($ch);
        curl_close($ch);

        return is_null(json_decode($apiResponse, true))
            ? $apiResponse
            : json_decode($apiResponse, true);

    }

    public function copy($from_path, $to_path, $allow_shared_folder = false, $autorename = false,
                         $allow_ownership_transfer = false)
    {
        $payload = [
            'from_path' => $from_path,
            'to_path' => $to_path,
            'allow_shared_folder' => $allow_shared_folder,
            'autorename' => $autorename,
            'allow_ownership_transfer' => $allow_ownership_transfer
        ];

        $header = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token
        ];

        $ch = curl_init('https://api.dropboxapi.com/2/files/copy_v2');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        $apiResponse = curl_exec($ch);
        curl_close($ch);

        return is_null(json_decode($apiResponse, true))
            ? $apiResponse
            : json_decode($apiResponse, true);

    }

    public function preview($path)
    {
        $headerArgs = json_encode([
            'path' => $path
        ]);

        $header = [
            'Content-Type:',
            'Authorization: Bearer ' . $this->token,
            'Dropbox-API-Arg: ' . $headerArgs
        ];

        $ch = curl_init('https://content.dropboxapi.com/2/files/get_preview');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $apiResponse = curl_exec($ch);
        curl_close($ch);

        return is_null(json_decode($apiResponse, true))
            ? $apiResponse
            : json_decode($apiResponse, true);

    }

    private function simpleCurl($url, $payload, $header, $type = 'normal', $requestMethod = 'POST', $fileHandler = null, $size = null)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $requestMethod);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if ($type == 'file'){
            curl_setopt($ch, CURLOPT_INFILE, $fileHandler);
            curl_setopt($ch, CURLOPT_INFILESIZE, $size);
        }

        $apiResponse = curl_exec($ch);
        curl_close($ch);

        return is_null(json_decode($apiResponse, true))
            ? $apiResponse
            : json_decode($apiResponse, true);

    }
}