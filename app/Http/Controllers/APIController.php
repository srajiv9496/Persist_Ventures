<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class APIController extends Controller
{
    public function upload(Request $request)
    {
        if ($request->hasFile('file')) {
            $csvFile = $request->file('file');
            $lines = file($csvFile->getRealPath(), FILE_IGNORE_NEW_LINES);
            array_shift($lines);

            foreach ($lines as $line) {
                list($name, $email, $username, $address, $role) = explode(',', $line);
                User::create([
                    'name' => $name,
                    'email' => $email,
                    'username' => $username,
                    'address' => $address,
                    'role' => $role,
                ]);
            }

            dispatch(new SendWelcomeEmails());

            return response()->json(['message' => 'Users uploaded successfully.']);
        } else {
            return response()->json(['error' => 'No file provided.'], 400);
        }
    }
}
