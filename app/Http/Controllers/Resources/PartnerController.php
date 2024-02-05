<?php

namespace App\Http\Controllers\Resources;

use App\Competition;
use App\Http\Controllers\Controller;
use App\Media;
use App\Page;
use App\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Intervention\Image\ImageManager;

class PartnerController extends Controller
{
    private $imageManager;

    public function __construct(ImageManager $manager)
    {
        $this->middleware('auth');
        $this->middleware('permission:partners')->only(['index', 'show']);
        $this->middleware('permission:partners.edit')->only(['edit', 'update']);
        $this->middleware('permission:partners.create')->only(['create', 'store', 'destroy', 'showGenerateImage', 'doGenerateImage']);
        $this->imageManager = $manager;
    }

    public function index()
    {
        $partners = Partner::orderBy('id', 'desc')->paginate(config('custom.results_per_page'));

        return view('backoffice.pages.partners', [
            'partners' => $partners
        ]);
    }

    public function create()
    {
        return view('backoffice.pages.create_partner');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:50|string',
            'url' => 'required|max:255|string|url',
            'priority' => 'required|integer|min:1|max:100',
            'picture' => 'required|mimes:jpeg,jpg,png|max:20000',
            'visible' => 'required',
        ]);

        $picture = MediaController::storeImage(
            $request->file('picture'),
            str_replace(' ', ',', $request->input('name'))
        );

        $partner = Partner::create([
            'name' => $request->input('name'),
            'url' => $request->input('url'),
            'priority' => $request->input('priority'),
            'picture' => $picture,
            'visible' => $request->input('visible') == 'true',
        ]);

        return redirect(route('partners.show', ['partner' => $partner]));
    }

    /**
     * Display the specified resource.
     *
     * @return View
     */
    public function show(Partner $partner)
    {
        return view('backoffice.pages.partner', ['partner' => $partner]);
    }

    /**
     * Show the form for editing the specified resource.

     * @return View
     */
    public function edit(Partner $partner)
    {
        return view('backoffice.pages.edit_partner', ['partner' => $partner]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:50|string',
            'url' => 'required|max:255|string|url',
            'priority' => 'required|integer|min:1|max:100',
            'visible' => 'required',
        ]);

        $partner = Partner::findOrFail($id);
        $partner->name = $request->input('name');
        $partner->url = $request->input('url');
        $partner->priority = $request->input('priority');
        $partner->visible = $request->input('visible') == 'true';
        $partner->save();

        return redirect(route('partners.show', ['partner' => $partner]));
    }

    public function destroy(Partner $partner)
    {
        $partner->delete();

        $message = new MessageBag();
        $message->add('success', trans('success.model_deleted', ['model_name' => trans('models.partner')]));

        return redirect(route('partners.index'))->with(['popup_message' => $message]);
    }

    public function showGenerateImage(Partner $partner)
    {
        return view('backoffice.pages.generate_partner_image', ['partner' => $partner]);
    }

    public function doGenerateImage(Request $request, Partner $partner) {

        $validation_rules = [
            'text' => 'required|max:25|min:3|string',
            'color' => 'required|regex:/^#[0-9A-F]{6}$/i',
            'selected_media_id' => 'required|integer',
        ];

        $request->validate($validation_rules);

        $competitions = Competition::where('visible', true)->get();
        $media = Media::findOrFail($request->input('selected_media_id'));
        $partnerImg = $this->imageManager->make(public_path($partner->picture));
        $backgroundImg = $this->imageManager->make(public_path($media->url));
        $backgroundImg = $backgroundImg->fit(900, 900);
        $text = $request->input('text');
        $topLogo = null;
        foreach ($competitions as $competition) {
            if ($competition->name == $text) {
                $topLogo = $competition->picture;
            }
        }

        $text = Str::upper($text);
        $color = $request->input('color');
        $colorFill = $this->imageManager->canvas(900, 900, $color)->opacity(50);

        if (empty($topLogo)) {
            $topLogo = '/images/domingo_as_dez_logo_mono_shaddow.png';
        }

        $logo = $this->imageManager->make(public_path($topLogo));
        $logo = $logo->fit(170, 170);
        $base = $this->imageManager->canvas(900, 900);
        $base->insert($backgroundImg, 'center');
        $base->blur(30);
        $base->insert($colorFill, 'center');
        $base->insert(public_path('/images/watermark.png'));

        $fontSize = $this->getFontSize($text);

        $base->text($text, 454, 369, function($font) use ($fontSize) {
            $font->file(public_path('Roboto-Black.ttf'));
            $font->size($fontSize);
            $font->color("#282828");
            $font->align('center');
            $font->valign('center');
        });

        $base->text($text, 450, 365, function($font) use ($fontSize) {
            $font->file(public_path('Roboto-Black.ttf'));
            $font->size($fontSize);
            $font->color("#ffffff");
            $font->align('center');
            $font->valign('center');
        });

        $base->insert($logo, 'center', 0, -280);

        $partnerImg = $partnerImg->resize(650, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $base->insert($partnerImg, 'center', 0, 200);

        $base = $base->encode('jpg');

        $filename = 'parceiro-' . Str::slug($partner->name) . '-' . Str::slug($text) . '.jpg';
        $headers = [
            'Content-Type' => 'image/jpeg',
            'Content-Disposition' => 'attachment; filename=' . $filename,
        ];

        return response()->stream(function () use ($base) {
            echo $base;
        }, 200, $headers);
    }

    public function getFontSize(string $text) {
        $length = Str::length($text);

        return (((50 / 11) * -1) * $length) + 160;
    }
}
