<?php

namespace App\Http\Controllers;

use App\Models\Images;
use App\Models\User;
use Carbon\Exceptions\Exception;
use Google\Service\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class GoogleDriveController extends Controller
{
    public $gClient;

    function __construct()
    {

        $this->gClient = new \Google_Client();

        $this->gClient->setApplicationName('Web client 1'); // ADD YOUR AUTH2 APPLICATION NAME (WHEN YOUR GENERATE SECRATE KEY)
        $this->gClient->setClientId('305270936396-ii8cjbfvkmeofv2oq25qk2tmpme75epu.apps.googleusercontent.com');
        $this->gClient->setClientSecret('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');
        $this->gClient->setRedirectUri(route('google.login'));
        $this->gClient->setDeveloperKey('AIzaSyCa17lzupUzQ1c7ZkE8PzNXML-udKJe7os');
        $this->gClient->setScopes(array(
            'https://www.googleapis.com/auth/drive.file',
            'https://www.googleapis.com/auth/drive'
        ));

        $this->gClient->setAccessType("offline");
        $this->gClient->setApprovalPrompt("force");
    }

    public function index()
    {
        $images = Images::paginate(10);
        return view('welcome', compact('images'));
    }

    public function loginAlert(Request $request)
    {
    }

    public function delete($id, Request $request)
    {
        $google_oauthV2 = new \Google_Service_Oauth2($this->gClient);
        if ($request->get('code')) {
            $this->gClient->authenticate($request->get('code'));
            $request->session()->put('token', $this->gClient->getAccessToken());
        }
        if ($request->session()->get('token')) {
            $this->gClient->setAccessToken($request->session()->get('token'));
        }

        if ($this->gClient->getAccessToken()) {
            $user = User::find(1);
            $user->access_token = json_encode($request->session()->get('token'));
            $user->save();
            Images::where('pic', $id)->delete();
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/drive/v3/files/' . $id);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

            curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

            $headers = array();
            $headers[] = 'Authorization: Bearer ' . $user->access_token;
            $headers[] = 'Accept: application/json';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            }
            curl_close($ch);
            return redirect()->back();
        } else {
            // FOR GUEST USER, GET GOOGLE LOGIN URL
            $authUrl = $this->gClient->createAuthUrl();
            return redirect()->to($authUrl);
        }
    }

    public function googleLogin(Request $request)
    {
        $google_oauthV2 = new \Google_Service_Oauth2($this->gClient);
        if ($request->get('code')) {
            $this->gClient->authenticate($request->get('code'));
            $request->session()->put('token', $this->gClient->getAccessToken());
        }
        if ($request->session()->get('token')) {
            $this->gClient->setAccessToken($request->session()->get('token'));
        }
        if ($this->gClient->getAccessToken()) {

            //FOR LOGGED IN USER, GET DETAILS FROM GOOGLE USING ACCES
            $user = User::find(1);
            $user->access_token = json_encode($request->session()->get('token'));
            $user->save();
            return redirect('/');
            // $this->googleDriveFilePpload();
        } else {
            // FOR GUEST USER, GET GOOGLE LOGIN URL
            $authUrl = $this->gClient->createAuthUrl();
            return redirect()->to($authUrl);
        }
    }

    public function fileupload()
    {
        return view('welcome');
    }
    public function googleDriveFilePpload(Request $request)
    {
        $service = new \Google_Service_Drive($this->gClient);
        $user = User::find(1);
        $this->gClient->setAccessToken(json_decode($user->access_token, true));
        if ($this->gClient->isAccessTokenExpired()) {
            // SAVE REFRESH TOKEN TO SOME VARIABLE
            $refreshTokenSaved = $this->gClient->getRefreshToken();
            // UPDATE ACCESS TOKEN
            $this->gClient->fetchAccessTokenWithRefreshToken($refreshTokenSaved);
            // PASS ACCESS TOKEN TO SOME VARIABLE
            $updatedAccessToken = $this->gClient->getAccessToken();
            // APPEND REFRESH TOKEN
            $updatedAccessToken['refresh_token'] = $refreshTokenSaved;
            // SET THE NEW ACCES TOKEN
            $this->gClient->setAccessToken($updatedAccessToken);
            $user->access_token = $updatedAccessToken;
            $user->save();
        }

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extension;
            $images = $file->move('images/', $filename);
            $folder = '1nPhcJvZb37-YRQ7xSB7E_xx38-koMfJ_';
            $file = new \Google_Service_Drive_DriveFile(array('name' => $filename, 'parents' => array($folder)));
            $result = $service->files->create($file, array(
                'data' => file_get_contents(public_path($images)),
                'mimeType' => 'application/octet-stream',
                'uploadType' => 'media'
            ));
            unlink(public_path($images));
            $imagesURL = $result->id;
            $image = new Images();
            $image->pic = $imagesURL;
            $image->save();
        }
        return redirect('/');
    }
}
