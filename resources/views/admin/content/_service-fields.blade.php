<div class="grid sm:grid-cols-2 gap-5">
    <div class="sm:col-span-2">
        <label class="field-label">Name <span class="text-rose-500">*</span></label>
        <input name="name" value="{{ $service->name }}" class="field" required>
    </div>

    <div>
        <label class="field-label">Speciality</label>
        <select name="speciality_id" class="field">
            <option value="">None</option>
            @foreach($specialities as $speciality)
                <option value="{{ $speciality->id }}" @selected($service->speciality_id == $speciality->id)>{{ $speciality->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="field-label">Duration (minutes) <span class="text-rose-500">*</span></label>
        <input type="number" name="duration_minutes" value="{{ $service->duration_minutes ?? 30 }}" min="10" max="480" step="5" class="field" required>
    </div>

    <div>
        <label class="field-label">Price from <span class="text-rose-500">*</span></label>
        <input type="number" step="0.01" name="price_from" value="{{ $service->price_from ?? 0 }}" class="field" required>
    </div>

    <div>
        <label class="field-label">Price to</label>
        <input type="number" step="0.01" name="price_to" value="{{ $service->price_to }}" class="field">
    </div>

    <div class="sm:col-span-2">
        <label class="field-label">Summary</label>
        <input name="summary" value="{{ $service->summary }}" maxlength="255" class="field">
    </div>

    <div class="sm:col-span-2">
        <label class="field-label">Full description</label>
        <textarea name="description" rows="4" class="field">{{ $service->description }}</textarea>
    </div>

    <div>
        <label class="field-label">Image</label>
        <input type="file" name="image" accept="image/*" class="field !py-2 text-sm">
    </div>

    <div>
        <label class="field-label">Sort order</label>
        <input type="number" name="sort_order" value="{{ $service->sort_order ?? 0 }}" class="field">
    </div>

    <div class="sm:col-span-2 flex flex-wrap gap-5">
        <label class="flex items-center gap-2.5 text-sm cursor-pointer">
            <input type="checkbox" name="is_active" value="1" @checked($service->is_active ?? true) class="w-4 h-4 rounded border-pine/25 text-pine focus:ring-jade">
            Visible on the website
        </label>
        <label class="flex items-center gap-2.5 text-sm cursor-pointer">
            <input type="checkbox" name="is_featured" value="1" @checked($service->is_featured) class="w-4 h-4 rounded border-pine/25 text-pine focus:ring-jade">
            Feature on the homepage
        </label>
    </div>
</div>
