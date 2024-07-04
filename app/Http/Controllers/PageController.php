<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use Session;
use Illuminate\Support\Facades\Mail;
use App\Mail\ImportConfirmationMail;
use Illuminate\Support\Facades\Auth;


class PageController extends Controller
{
    public function index(){
        return view('index');
    }

    public function uploadFile(Request $request) {
        if ($request->input('submit') != null) {
            $file = $request->file('file');
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $tempPath = $file->getRealPath();
            $fileSize = $file->getSize();
            $mimeType = $file->getMimeType();

            $valid_extension = array("csv");
            $maxFileSize = 2097152; // 2MB

            if (in_array(strtolower($extension), $valid_extension)) {
                if ($fileSize <= $maxFileSize) {
                    $location = 'uploads';
                    $file->move($location, $filename);

                    $filepath = public_path($location . "/" . $filename);
                    $file = fopen($filepath, "r");

                    $importData_arr = array();
                    $i = 0;

                    // Skip the first line (header)
                    fgetcsv($file, 1000, ",");

                    while (($filedata = fgetcsv($file, 1000, ",")) !== false) {
                        $num = count($filedata);
                        for ($c = 0; $c < $num; $c++) {
                            $importData_arr[$i][] = $filedata[$c];
                        }
                        $i++;
                    }

                    fclose($file);

                    foreach ($importData_arr as $importData) {
                        // Trim the data to remove any leading or trailing spaces
                        $name = trim($importData[0]);
                        $email = trim($importData[1]);
                        $username = trim($importData[2]);
                        $address = trim($importData[3]);
                        $role = trim($importData[4]);

                        // Validate the role value
                        if (in_array($role, ['USER', 'ADMIN'])) {
                            $insertData = array(
                                "name" => $name,
                                "email" => $email,
                                "username" => $username,
                                "address" => $address,
                                "role" => $role
                            );

                            Page::insertData($insertData);
                        } else {
                            // Handle invalid role values
                            Session::flash('message', 'Invalid role value found in CSV: ' . $role);
                        }
                    }

                    // Get the uploader's email
                    $uploaderEmail = $request->user() ? $request->user()->email : $request->input('email');

                    // Send the confirmation email to the uploader
                    if ($uploaderEmail) {
                        Mail::to($uploaderEmail)->send(new ImportConfirmationMail($uploaderEmail));
                        Session::flash('message', 'Import Successful. A confirmation email has been sent to you.');
                    } else {
                        Session::flash('message', 'Import Successful, but no email provided for confirmation.');
                    }
                } else {
                    Session::flash('message', 'File too large. File must be less than 2MB.');
                }
            } else {
                Session::flash('message', 'Invalid File Type. Import only .csv file.');
            }
        }

        return redirect('/');
    }

    public function showUsers()
    {
        $users = Page::all();
        return view('users.index', ['users' => $users]);
    }

    public function backupDatabase()
    {
        $backupFileName = 'backup_' . date('Y_m_d_H_i_s') . '.sql';
        $backupFilePath = storage_path('app/' . $backupFileName);

        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s %s > %s',
            env('DB_USERNAME'),
            env('DB_PASSWORD'),
            env('DB_HOST'),
            env('DB_DATABASE'),
            $backupFilePath
        );

        $returnVar = null;
        $output = null;
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            return response()->json(['error' => 'Backup failed'], 500);
        }
        return response()->download($backupFilePath)->deleteFileAfterSend(true);
    }
}
