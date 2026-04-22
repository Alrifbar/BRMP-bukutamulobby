<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FormField;
use Illuminate\Http\Request;

class FormBuilderController extends Controller
{
    public function index()
    {
        $fields = FormField::orderBy('order')->get();
        return view('admin.form-builder.index', compact('fields'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:form_fields,name',
            'label' => 'required|string',
            'placeholder' => 'nullable|string',
            'type' => 'required|string',
            'options' => 'nullable|array',
            'is_required' => 'boolean',
            'is_visible' => 'boolean',
        ]);

        $maxOrder = FormField::max('order') ?? 0;
        $validated['order'] = $maxOrder + 1;
        $validated['is_core'] = false;

        FormField::create($validated);

        return redirect()->back()->with('success', 'Kolom baru berhasil ditambahkan.');
    }

    public function update(Request $request, FormField $formField)
    {
        $validated = $request->validate([
            'label' => 'required|string',
            'placeholder' => 'nullable|string',
            'type' => 'required|string',
            'options' => 'nullable|array',
            'is_required' => 'boolean',
            'is_visible' => 'boolean',
        ]);

        if ($formField->is_core) {
            // Core fields cannot change name or type easily if it breaks logic, 
            // but we allow changing label, placeholder, required, visible.
            unset($validated['type']); 
        }

        $formField->update($validated);

        return redirect()->back()->with('success', 'Kolom berhasil diperbarui.');
    }

    public function destroy(FormField $formField)
    {
        if ($formField->is_core) {
            return redirect()->back()->with('error', 'Kolom inti tidak dapat dihapus.');
        }

        $formField->delete();

        return redirect()->back()->with('success', 'Kolom berhasil dihapus.');
    }

    public function reorder(Request $request)
    {
        $orders = $request->input('orders');
        foreach ($orders as $order) {
            FormField::where('id', $order['id'])->update(['order' => $order['order']]);
        }

        return response()->json(['success' => true]);
    }
}
