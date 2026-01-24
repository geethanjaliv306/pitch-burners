<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutUsContent;
use App\Models\Gallery;
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
        'images.*' => 'required|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            // Generate a unique filename
            $imageName = time() . '_' . $image->getClientOriginalName();

            // Move the image to public/uploads/gallery directory
            $image->move(public_path('uploads/gallery'), $imageName);

            // Save only the file name in the database
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

        return redirect()->route('cms-gallery')->with('success', 'Gallery item deleted successfully.');
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
        'images.*' => 'required|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            // Generate a unique filename
            $imageName = time() . '_' . $image->getClientOriginalName();

            // Move the image to public/uploads/gallery directory
            $image->move(public_path('uploads/gallery'), $imageName);

            // Save only the file name in the database
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
        'images.*' => 'required|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            // Generate a unique filename
            $imageName = time() . '_' . $image->getClientOriginalName();

            // Move the image to public/uploads/partners directory
            $image->move(public_path('uploads/partners'), $imageName);

            // Save only the file name in the database
            Partner::create([
                'title' => $request->title,
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

        return redirect()->route('cms-partners')->with('success', 'Partner item deleted successfully.');
    }

    public function partners_update_title(Request $request, $id)
     {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $partner = Partner::findOrFail($id);
        Partner::where('title', $partner->title)->update(['title' => $request->title]);

        return redirect()->route('cms-partners')->with('success', 'Partner title updated successfully.');
    }

 public function partners_add_images(Request $request, $title)
{
    $request->validate([
        'images.*' => 'required|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            // Generate a unique filename
            $imageName = time() . '_' . $image->getClientOriginalName();

            // Move the image to public/uploads/partners directory
            $image->move(public_path('uploads/partners'), $imageName);

            // Save only the file name in the database
            Partner::create([
                'title' => $title,
                'image' => $imageName,
            ]);
        }
    }

    return redirect()->route('cms-partners')->with('success', 'Images added successfully.');
}


}
