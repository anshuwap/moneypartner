<?php


if (!function_exists('singleFile')) {

    function singleFile($file, $folder)
    {
        if ($file) {
            if (!file_exists($folder))
            mkdir($folder, 0777, true);

            $destinationPath = $folder;
            $profileImage = date('YmdHis') . "." . $file->getClientOriginalExtension();
            $file->move($destinationPath, $profileImage);
            $fileName = "$profileImage";
            return $fileName;
        }
        return false;
    }
}
