<?php

namespace App\Http\Controllers;

use App\Models\Whatsapp;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\DataTables;

class WhatsappController extends Controller
{
    public function __construct(Whatsapp $model)
    {
        $this->title            = 'WhatsApp';
        $this->subtitle         = 'WhatsApp Client';
        $this->model_request    = WhatsappClientRequest::class;
        $this->folder           = '';
        $this->relation         = [];
        $this->model            = $model;
        $this->withTrashed      = false;
    }

    public function formData()
    {
        return array('list_tipe_ar' => $this->list_tipe_ar());
    }

    public function ajaxData()
    {
        if ($this->withTrashed) {
            $mapped                     = $this->model->withTrashed()->query();
        } else {
            $mapped                     = $this->model->query();
        }
        return DataTables::of($mapped)
            ->editColumn('status', function ($data) {
                return client_status($data->status);
            })
            ->addColumn('service', function ($data) {
                return '<div class="d-none" id="d-qrcode-' . $data->session_id . '"></div><div class="text-center serviceClass service-' . $data->session_id . '"></div>';
            })
            ->rawColumns(['status', 'service'])
            ->toJson();
    }

    public function autoreply(Request $request, $id)
    {
        $mapped                         = AutoReply::where('wa_id', $id);

        return DataTables::of($mapped)
            ->editColumn('response_media', function ($data) {
                $btn = $data->response_media_name ?? "Open Link";
                return empty($data->response_media) ? "" : "<a href=\"" . $data->response_media . "\" target=\"_blank\">" . $btn . "</a>";
            })
            ->editColumn('response_text', function ($data) {
                if (empty($data->response_api)) {
                    return $data->response_text;
                } else {
                    return "<span class='badge bg-warning text-warning bg-opacity-20'>GO TO API</span> " . $data->response_api;
                }
            })
            ->rawColumns(['response_media', 'response_text'])
            ->toJson();
    }

    public function getButtonOption(Request $request)
    {
        $data           = $request->all();
        $model = Whatsapp::find($request->id);
        $view           = [
            'status'                    => true,
            'view'                              => view('whatsapp.menuoption')->with(['id' => $data['id'], 'session_id' => $model->session_id, 'url' => $data['buttons'], 'services' => $model->status, 'urlsend' => 'get.form.send', 'urlapi' => 'get.form.api'])->render()
        ];

        return response()->json($view);
    }

    public function customStore($data, $model)
    {
        $model->status = 0;
        $model->session_key = strtoupper(substr(md5(uniqid(rand(), true)), 0, 20));
        $model->save();
    }

    public function getSendMsgForm($id)
    {
        $view['id'] = $id;
        $view['url'] = 'send.msg';
        $response           = [
            'status'            => true,
            'view'              => view('whatsapp.form-send-msg')->with($view)->render(),
        ];
        return response()->json($response);
    }

    public function getApiForm($id)
    {
        $view['id'] = $id;
        $view['client'] = Whatsapp::where('session_id', $id)->first();
        $response           = [
            'status'            => true,
            'view'              => view('whatsapp.form-api')->with($view)->render(),
        ];
        return response()->json($response);
    }

    public function sendMsg(Request $request, $id)
    {
        try {

            DB::beginTransaction();
            if (empty($request->text) || empty($request->number)) {
                $msgerror = 'Number & text can\'t empty.';
                $error = ValidationException::withMessages([
                    'msg' => $msgerror
                ]);
                throw $error;
            }

            $client = Whatsapp::where('session_id', $id)->first();
            if (empty($client)) {
                $msgerror = 'Session ID not found.';
                $error = ValidationException::withMessages([
                    'msg' => $msgerror
                ]);
                throw $error;
            }
            $wa = new MiaBaileysHelper;
            $result = $wa->sendMessage($id, $client->session_key, $request->number, $request->text, $request->file_url ?? "", $request->file_name ?? "", false);
            if (!$result['status']) {
                $msgerror = $result['msg'];
                $error = ValidationException::withMessages([
                    'msg' => $msgerror
                ]);
                throw $error;
            }
            DB::commit();
            if ($request->ajax()) {
                $response           = [
                    'status'            => true,
                    'msg'               => 'Message Sent!',
                ];
                return response()->json($response);
            } else {
                return $this->redirectSuccess(__FUNCTION__, false);
            }
        } catch (Exception $e) {

            DB::rollback();
            if ($request->ajax()) {
                $response           = [
                    'status'            => false,
                    'msg'               => $e->getMessage(),
                ];
                return response()->json($response);
            } else {
                return $this->redirectBackWithError($e->getMessage());
            }
        }
    }

    public function update(Request $request, $id)
    {

        try {

            DB::beginTransaction();

            $data  = $this->getRequest();
            unset($data['session_id']);

            if ($this->withTrashed) {
                $model = $this->model->withTrashed()->findOrFail($id);
            } else {
                $model = $this->model->findOrFail($id);
            }

            $model->fill($data);

            $model->save();

            if (method_exists($this, 'customUpdate')) {
                $this->customUpdate($data, $model);
            }

            $log_helper     = new LogHelper;

            $log_helper->storeLog('edit', $model->id, $this->subtitle);

            DB::commit();
            if ($request->ajax()) {
                $response           = [
                    'status'            => true,
                    'msg'               => 'Data Saved.',
                ];
                return response()->json($response);
            } else {
                return $this->redirectSuccess(__FUNCTION__, false);
            }
        } catch (Exception $e) {

            DB::rollback();
            if ($request->ajax()) {
                $response           = [
                    'status'            => false,
                    'msg'               => $e->getMessage(),
                ];
                return response()->json($response);
            } else {
                return $this->redirectBackWithError($e->getMessage());
            }
        }
    }

    public function customUpdate($data, $model)
    {
        if (!empty($data['change_key'])) {
            $newkey = strtoupper(substr(md5(uniqid(rand(), true)), 0, 20));
            $model->session_key = $newkey;
            $model->save();
            //send to Mia-express
            $client = new \GuzzleHttp\Client();
            $endpoint = env('WA_CLIENT_URL');
            $endpoint .= "stop-service";
            $response = $client->request('POST', $endpoint, ['form_params' => [
                'session_id' => $model->session_id,
                'session_key' => $model->session_key,
                'private_key' => env('WA_PRIVATE_KEY'),
                'session_delete' => 0
            ]]);
            //start with new key
            $endpoint = env('WA_CLIENT_URL');
            $endpoint .= "start-service";
            $response = $client->request('POST', $endpoint, ['form_params' => [
                'session_id' => $model->session_id,
                'session_key' => $model->session_key,
                'private_key' => env('WA_PRIVATE_KEY')
            ]]);
        }
    }

    public function start(Request $request)
    {
        $model = $this->model::findOrFail($request->id);
        if ($model->status == 1) {
            $view               = [
                'status'                        => false,
                'msg'                           => 'Services already started.'
            ];
            return response()->json($view);
        }
        try {
            DB::beginTransaction();
            $client = new \GuzzleHttp\Client();
            $endpoint = env('WA_CLIENT_URL');
            $endpoint .= "start-service";
            $response = $client->request('POST', $endpoint, ['form_params' => [
                'session_id' => $model->session_id,
                'session_key' => $model->session_key,
                'private_key' => env('WA_PRIVATE_KEY')
            ]]);

            if ($response->getStatusCode() <> 200 && $response->getStatusCode() <> 201) {
                $msgerror = 'Something went wrong, Status Code ' . $response->getStatusCode() . '.';
                $error = ValidationException::withMessages([
                    'msg' => $msgerror
                ]);
                throw $error;
            }
            $model->status = 1;
            $model->save();
            DB::commit();
            if ($response->getStatusCode() <> 200) {
                $view           = [
                    'status'                    => true,
                    'msg'                               => 'Services already started.'
                ];
            } else {
                $view           = [
                    'status'                    => true,
                    'msg'                               => 'Services started, please wait a few seconds for services ready.'
                ];
            }
            return response()->json($view);
        } catch (Exception $e) {
            DB::rollback();
            $response           = [
                'status'            => false,
                'msg'               => $e->getMessage(),
            ];
            return response()->json($response);
        }
    }

    public function stop(Request $request)
    {
        $model = $this->model::findOrFail($request->id);
        if (empty($model->status) || $model->status == 0) {
            $view               = [
                'status'                        => false,
                'msg'                           => 'Services already stopped.'
            ];
            return response()->json($view);
        }
        try {
            DB::beginTransaction();
            $client = new \GuzzleHttp\Client();
            $endpoint = env('WA_CLIENT_URL');
            $endpoint .= "stop-service";
            $response = $client->request('POST', $endpoint, ['form_params' => [
                'session_id' => $model->session_id,
                'session_key' => $model->session_key,
                'private_key' => env('WA_PRIVATE_KEY'),
                'session_delete' => 0
            ]]);

            if ($response->getStatusCode() <> 200 && $response->getStatusCode() <> 201) {
                $msgerror = 'Error, Status Code ' . $response->getStatusCode() . '.';
                $error = ValidationException::withMessages([
                    'msg' => $msgerror
                ]);
                throw $error;
            }
            $model->status = 0;
            $model->save();
            DB::commit();
            if ($response->getStatusCode() <> 200) {
                $view           = [
                    'status'                    => true,
                    'msg'                               => 'Services already stopped.'
                ];
            } else {
                $view           = [
                    'status'                    => true,
                    'msg'                               => 'Services stopped.'
                ];
            }
            return response()->json($view);
        } catch (Exception $e) {
            DB::rollback();
            $response           = [
                'status'            => false,
                'msg'               => $e->getMessage(),
            ];
            return response()->json($response);
        }
    }

    public function customDestroy($model)
    {
        if ($model->status == 1) {
            $client = new \GuzzleHttp\Client();
            $endpoint = env('WA_CLIENT_URL');
            $endpoint .= "stop-service";
            $response = $client->request('POST', $endpoint, ['form_params' => [
                'session_id' => $model->session_id,
                'session_key' => $model->session_key,
                'private_key' => env('WA_PRIVATE_KEY'),
                'session_delete' => 1
            ]]);
        }
    }


    public function getDetailAPI(Request $request)
    {
        $client = $this->model::where('session_id', $request->id)->first();
        $data['client'] = $client;
        $response           = [
            'status'            => true,
            'view'              => view('whatsapp.send-api')->with($data)->render(),
        ];
        return response()->json($response);
    }
}
