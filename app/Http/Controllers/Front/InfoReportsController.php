<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\InfoReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;

class InfoReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only('delete');
    }

    public function create()
    {
        return view('front.pages.create_info_report');
    }

    public function store(Request $request)
    {
        $messages = new MessageBag();

        $request->validate([
            'content' => 'string|required|min:10|max:500',
            'source' => 'string|required|min:10|max:155',
            'anonymous' => 'required',
            'g-recaptcha-response' => 'required|recaptcha',
        ]);

        $tries = 0;
        do {
            $code = strtoupper(str_random(9));
            $tries++;
            $existingInfo = InfoReport::where('code', $code)->first();

            if (empty($existingInfo))
                break;

            if ($tries > 5) {
                $messages->add('error', 'Não foi possível gerar um código único. Volte a tentar mais tarde');
                return redirect()->back()->with('popup_message', $messages);
            }
        } while (true);

        $user_id = null;
        if ($request->input('anonymous') !== "true") {
            $user = Auth::user();
            $user_id = $user->id;
        }

        InfoReport::create([
            'code' => $code,
            'user_id' => $user_id,
            'status' => 'sent',
            'source' => $request->input('source'),
            'content' => $request->input('content')
        ]);

        if (!empty($user_id)) {
            $message = "Informação enviada com sucesso, pode consultar o estado da informação no seu perfil de utilizador";
        } else {
            $message = "Informação enviada com sucesso, guarde o código « $code » para consultar o estado da informação no futuro";
        }

        $messages->add('success', $message);

        return redirect(route('info.create'))->with('popup_message', $messages);
    }

    public function show(Request $request)
    {
        $request->validate([
            'code' => 'required|string|min:9|max:9'
        ]);

        $code = strtoupper($request->input('code'));
        $info = InfoReport::where('code', $code)->where('status', '!=', 'deleted')->first();

        return view('front.pages.show_info_report', ['info' => $info, 'code' => $code]);
    }

    public function delete(Request $request)
    {
        $messages = new MessageBag();
        $user = Auth::user();
        $code = $request->input('code');
        $info = InfoReport::where('code', $code)->first();
        if (!empty($info)) {
            if ($user->id == $info->user_id) {
                $info->status = 'deleted';
                $info->save();
                $messages->add('success', "Informação com o código '$code' foi apagada com sucesso");
            } else {
                $messages->add('error', 'Permissões insuficientes para apagar essa informação');
            }
        } else {
            $messages->add('error', 'Código inválido, nada foi apagado');
        }

        return redirect(route('front.userprofile.edit'))->with('popup_message', $messages);
    }
}
