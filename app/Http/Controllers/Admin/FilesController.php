<?php

namespace App\Http\Controllers\Admin;

use App\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFilesRequest;
use App\Http\Requests\Admin\UpdateFilesRequest;
use App\Http\Controllers\Traits\FileUploadTrait;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Input;
use Faker\Provider\Uuid;

class FilesController extends Controller
{
    use FileUploadTrait;

    /**
     * Display a listing of File.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Gate::allows('file_access')) {
            return abort(401);
        }
        if ($filterBy = Input::get('filter')) {
            if ($filterBy == 'all') {
                Session::put('File.filter', 'all');
            } elseif ($filterBy == 'my') {
                Session::put('File.filter', 'my');
            }
        }

        if (request('show_deleted') == 1) {
            if (!Gate::allows('file_delete')) {
                return abort(401);
            }
            $files = File::onlyTrashed()->get();
        } else {
            $files = File::all();
        }
        $user = Auth::getUser();
        $userFilesCount = File::where('created_by_id', $user->id)->count();

        return view('admin.files.index', compact('files', 'userFilesCount'));
    }

    /**
     * Show the form for creating new File.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Gate::allows('file_create')) {
            return abort(401);
        }
        
        $roleId = Auth::getUser()->role_id;
        $userFilesCount = File::where('created_by_id', Auth::getUser()->id)->count();
        if ($roleId == 2 && $userFilesCount > 5) {
            return redirect('/admin/files');
        }

        $folders = \App\Folder::get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        $created_bies = \App\User::get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');

        return view('admin.files.create', compact('folders', 'created_bies', 'userFilesCount', 'roleId'));
    }

    /**
     * Store a newly created File in storage.
     *
     * @param  \App\Http\Requests\StoreFilesRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreFilesRequest $request)
    {
        if (!Gate::allows('file_create')) {
            return abort(401);
        }
        
            $request = $this->saveFiles($request);

            $data = $request->all();
            $fileIds = $request->input('filename_id');

            foreach ($fileIds as $fileId) {
                $file = File::create([
                    'id' => $fileId,
                    'uuid' => (string)\Webpatser\Uuid\Uuid::generate(),
                    'folder_id' => $request->input('folder_id'),
                    'created_by_id' => Auth::getUser()->id

                ]);
            }

            foreach ($request->input('filename_id', []) as $index => $id) {
                $model = config('laravel-medialibrary.media_model');
                $file = $model::find($id);
                $file->model_id = $file->id;
                $file->save();
            }
            return redirect()->route('admin.files.index');

    }


    /**
     * Show the form for editing File.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */

    /**
     * Update File in storage.
     *
     * @param  \App\Http\Requests\UpdateFilesRequest $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateFilesRequest $request, $id)
    {
        if (!Gate::allows('file_edit')) {
            return abort(401);
        }
        $request = $this->saveFiles($request);
        $file = File::findOrFail($id);
        $file->update($request->all());


        $media = [];
        foreach ($request->input('filename_id', []) as $index => $id) {
            $model = config('laravel-medialibrary.media_model');
            $file = $model::find($id);
            $file->model_id = $file->id;
            $file->save();
            $media[] = $file->toArray();
        }
        $file->updateMedia($media, 'filename');

        return redirect()->route('admin.files.index');
    }


    /**
     * Display File.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */


    /**
     * Remove File from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!Gate::allows('file_delete')) {
            return abort(401);
        }
        $file = File::findOrFail($id);
        $file->deletePreservingMedia();

        return redirect()->route('admin.files.index');
    }

    /**
     * Delete all selected File at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (!Gate::allows('file_delete')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = File::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->deletePreservingMedia();
            }
        }
    }


    /**
     * Restore File from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (!Gate::allows('file_delete')) {
            return abort(401);
        }
        $file = File::onlyTrashed()->findOrFail($id);
        $file->restore();

        return redirect()->route('admin.files.index');
    }

    /**
     * Permanently delete File from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (!Gate::allows('file_delete')) {
            return abort(401);
        }
        $file = File::onlyTrashed()->findOrFail($id);
        $file->forceDelete();

        return redirect()->route('admin.files.index');
    }
}
