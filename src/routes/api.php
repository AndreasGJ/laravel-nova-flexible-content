<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


Route::post('/upload-files', function(Request $request) {
    $request->validate([
        'files' => 'required|array',
        'files.*' => 'required|image'
    ]);

    $paths = [];
    if ($request->files && $request->files->count() > 0) {
        foreach ($request->file('files') as $index => $f) {
            $path = $f->store('public/images');

            $paths[] = Storage::url($path);
        }
    } else {
        $path = $request->file('files')[0]->store('public/images');
        $paths[] = Storage::url($path);
    }

    return [
        'status' => 1,
        'fileurls' => $paths
    ];
});

Route::post('/delete-files', function(Request $request){
    $request->validate([
        'fileurls' => 'required|array',
        'fileurls.*' => 'required|string'
    ]);

    $deleted = true;

    foreach ($request->fileurls as $fileurl) {
        $filepath = str_replace('/storage/', 'public/', $fileurl);

        if (!Storage::delete($filepath)) {
            $deleted = false;
        }
    }


    return [
        'status' => $deleted ? 1 : 0,
    ];
});
