<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutUsContent;
use App\Models\Gallery;
use App\Models\MailContent;
use App\Models\Partner;
use App\Models\Tournament;
use App\Models\Winner;
use Illuminate\Http\Request;

class ContentManageController extends Controller
{
    public function index()
     {
        $content = AboutUsContent::first();
        return view('cms.about-us.index', compact('content'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'banner_text' => 'nullable|string',
            'sub_details1' => 'nullable|string',
            'sub_details2' => 'nullable|string',
            'sub_details3' => 'nullable|string',
            'title1' => 'nullable|string',
            'title2' => 'nullable|string',
            'title3' => 'nullable|string',
        ]);

        $content = AboutUsContent::firstOrNew();

        $content->fill($request->only(['banner_text', 'sub_details1', 'sub_details2', 'sub_details3', 'title1', 'title2', 'title3']));

        $content->save();

        return redirect()->route('cms-about')->with('success', 'Content updated successfully');
    }


    public function winners()
    {
        $winners = Winner::with('tournament')->paginate(4);
        $tournaments = Tournament::all();
        return view('cms.winners.index', compact('winners', 'tournaments'));
    }


    public function store(Request $request)
     {
        $request->validate([
            'position' => 'required|string',
            'team_name' => 'string',
            'prize' => 'required|string',
           'additional_info' => 'nullable|string',
        'tournament_id' => 'required|exists:tournaments,id',
        ]);

        Winner::create($request->all());

        return redirect()->route('cms-winners')->with('success', 'Winner added successfully');
    }

    public function update_winner(Request $request, $id)
     {
     $request->validate([
        'position' => 'required|string',
        'team_name' => 'string',
        'prize' => 'required|string',
        'additional_info' => 'nullable|string',
        'tournament_id' => 'required|exists:tournaments,id',
     ]);

        $winner = Winner::findOrFail($id);
        $winner->update([
            'position' => $request->input('position'),
            'team_name' => $request->input('team_name'),
            'prize' => $request->input('prize'),
            'additional_info' => $request->input('additional_info'),
            'tournament_id' => $request->input('tournament_id'),
        ]);

        return redirect()->route('cms-winners')->with('success', 'Winner updated successfully');
    }

    public function destroy($id)
     {
        Winner::findOrFail($id)->delete();
        return redirect()->route('cms-winners')->with('success', 'Winner removed successfully');
    }

    public function gallery_cms()
     {
        $galleries = Gallery::all()->groupBy('title');
        return view('cms.gallery.index', compact('galleries'));
    }

 public function gallery_store(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'images.*' => 'required|image|mimes:jpg,jpeg,png,svg',
    ]);

    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            // Generate a unique name for the image
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

            // Compress the image
            $compressedImage = \Image::make($image)
                ->resize(1200, null, function ($constraint) {
                    $constraint->aspectRatio(); // Maintain aspect ratio
                    $constraint->upsize(); // Prevent upsizing
                })
                ->encode('jpg', 75); // Compress to 75% quality

            // Save the compressed image to the uploads folder
            $imagePath = public_path('uploads/gallery/' . $imageName);
            $compressedImage->save($imagePath);

            // Store the image data in the database
            Gallery::create([
                'title' => $request->title,
                'image' => $imageName,
            ]);
        }
    }

    return redirect()->route('cms-gallery')->with('success', 'Gallery images added successfully.');
}


    public function gallery_delete($id)
     {
        $gallery = Gallery::findOrFail($id);
        $gallery->delete();

        return redirect()->route('cms-gallery')->with('success', 'Gallery images deleted successfully.');
    }

    public function gallery_update_title(Request $request, $id)
     {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $gallery = Gallery::findOrFail($id);
        Gallery::where('title', $gallery->title)->update(['title' => $request->title]);

        return redirect()->route('cms-gallery')->with('success', 'Gallery title updated successfully.');
    }

  public function gallery_add_images(Request $request, $title)
{
    $request->validate([
        'images.*' => 'required|image|mimes:jpg,jpeg,png,svg',
    ]);

    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            // Generate a unique name for the image
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

            // Compress the image
            $compressedImage = \Image::make($image)
                ->resize(1200, null, function ($constraint) {
                    $constraint->aspectRatio(); // Maintain aspect ratio
                    $constraint->upsize(); // Prevent upsizing
                })
                ->encode('jpg', 75); // Compress to 75% quality

            // Save the compressed image to the uploads folder
            $imagePath = public_path('uploads/gallery/' . $imageName);
            $compressedImage->save($imagePath);

            // Store the image data in the database
            Gallery::create([
                'title' => $title,
                'image' => $imageName,
            ]);
        }
    }

    return redirect()->route('cms-gallery')->with('success', 'Images added successfully.');
}

    public function partners_cms()
     {
        $partners = Partner::all()->groupBy('title');
        return view('cms.partners.index', compact('partners'));
    }

   public function partners_store(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
       'link' => 'nullable|url|max:255', 
        'images.*' => 'required|image|mimes:jpg,jpeg,png,svg,heic',
    ]);

    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            // Generate a unique name for the image
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

            // Compress the image
            $compressedImage = \Image::make($image)
                ->resize(1200, null, function ($constraint) {
                    $constraint->aspectRatio(); // Maintain aspect ratio
                    $constraint->upsize(); // Prevent upsizing
                })
                ->encode('jpg', 75); // Compress to 75% quality

            // Save the compressed image to the uploads folder
            $imagePath = public_path('uploads/partners/' . $imageName);
            $compressedImage->save($imagePath);

            // Store the image data in the database
            Partner::create([
                'title' => $request->title,
                'link' => $request->link,
                'image' => $imageName,
            ]);
        }
    }

    return redirect()->route('cms-partners')->with('success', 'Partner images added successfully.');
}


    public function partners_delete($id)
     {
        $partner = Partner::findOrFail($id);
        $partner->delete();

        return redirect()->route('cms-partners')->with('success', 'Partner images deleted successfully.');
    }

    public function partners_update_title(Request $request, $id)
     {
        $request->validate([
            'title' => 'required|string|max:255',
            'link' => 'nullable|url|max:255',
        ]);

        $partner = Partner::findOrFail($id);
        Partner::where('title', $partner->title)->update(['title' => $request->title,  'link' => $request->link,]);

        return redirect()->route('cms-partners')->with('success', 'Partner title updated successfully.');
    }

   public function partners_add_images(Request $request, $title)
{
    $request->validate([
        'images.*' => 'required|image|mimes:jpg,jpeg,png,svg,heic',
    ]);

    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            // Generate a unique name for the image
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

            // Compress the image
            $compressedImage = \Image::make($image)
                ->resize(1200, null, function ($constraint) {
                    $constraint->aspectRatio(); // Maintain aspect ratio
                    $constraint->upsize(); // Prevent upsizing
                })
                ->encode('jpg', 75); // Compress to 75% quality

            // Save the compressed image to the uploads folder
            $imagePath = public_path('uploads/partners/' . $imageName);
            $compressedImage->save($imagePath);

            // Store the image data in the database
            Partner::create([
                'title' => $title,
                'image' => $imageName,
            ]);
        }
    }

    return redirect()->route('cms-partners')->with('success', 'Images added successfully.');
}

  
    public function edit()
    {
        $mailContent = MailContent::first();
        return view('cms.mailcontent.index', compact('mailContent'));
    }

    public function update_content(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'body_content' => 'required|string',
        ]);

        $mailContent = MailContent::firstOrCreate();
        $mailContent->update([
            'subject' => $request->subject,
            'body_content' => $request->body_content,
        ]);

        return back()->with('success', 'Email content updated successfully!');
    }

}
