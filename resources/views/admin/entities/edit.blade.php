@extends('layouts.admin')

@section('title', 'Редакция на обект (админ)')
@section('admin_page_title', 'Редакция на обект')

@section('content')
    <div class="mx-auto max-w-5xl space-y-6 bg-white">
        <div class="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-gray-300 p-4">
            <h1 class="text-2xl font-semibold text-black">Редакция на обект</h1>
            <a href="{{ route('admin.entities.show', $entity) }}" class="rounded border border-gray-400 px-3 py-2 text-sm text-black hover:bg-gray-100">
                Назад към прегледа
            </a>
        </div>

        @if ($errors->any())
            <div class="rounded-lg border border-gray-400 p-4">
                <ul class="list-disc space-y-1 pl-5 text-sm text-black">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.entities.update', $entity) }}" class="space-y-4 rounded-lg border border-gray-300 p-4">
            @csrf
            @method('PUT')

            <div class="space-y-1">
                <label class="block text-sm font-medium text-black">Тип обект</label>
                <p class="rounded border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-black">{{ $entityType->name }} ({{ $entityType->code }})</p>
            </div>

            <div class="space-y-1">
                <label for="entity_subtype_id" class="block text-sm font-medium text-black">Подтип на обекта</label>
                <select id="entity_subtype_id" name="entity_subtype_id" class="w-full rounded border border-gray-400 px-3 py-2 text-sm text-black">
                    <option value="">Изберете подтип</option>
                    @foreach ($entitySubtypes as $entitySubtype)
                        <option value="{{ $entitySubtype->id }}" @selected((string) old('entity_subtype_id', $entity->entity_subtype_id) === (string) $entitySubtype->id)>
                            {{ $entitySubtype->name }}
                        </option>
                    @endforeach
                </select>
                @error('entity_subtype_id')
                    <p class="text-sm text-black">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-1">
                <label for="status" class="block text-sm font-medium text-black">Статус</label>
                <select id="status" name="status" required class="w-full rounded border border-gray-400 px-3 py-2 text-sm text-black">
                    <option value="draft" @selected(old('status', $entity->status) === 'draft')>Чернова</option>
                    <option value="published" @selected(old('status', $entity->status) === 'published')>Публикуван</option>
                    <option value="hidden" @selected(old('status', $entity->status) === 'hidden')>Скрит</option>
                </select>
                @error('status')
                    <p class="text-sm text-black">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-1">
                <label for="country_id" class="block text-sm font-medium text-black">Държава</label>
                <select id="country_id" name="country_id" required class="w-full rounded border border-gray-400 px-3 py-2 text-sm text-black">
                    <option value="">Изберете държава</option>
                    @foreach ($countries as $country)
                        <option value="{{ $country->id }}" @selected((string) old('country_id', $entity->country_id) === (string) $country->id)>{{ $country->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1">
                <label for="place_search" class="block text-sm font-medium text-black">Населено място</label>
                <input
                    id="place_search"
                    type="text"
                    value="{{ $selectedPlace ? $selectedPlace->name.' ('.$selectedPlace->type.', '.$selectedPlace->municipality_name.', '.$selectedPlace->region_name.')' : '' }}"
                    placeholder="Въведете поне 2 символа за търсене"
                    autocomplete="off"
                    class="w-full rounded border border-gray-400 px-3 py-2 text-sm text-black"
                >
                <input id="place_id" name="place_id" type="hidden" value="{{ old('place_id', $entity->place_id) }}">
                @error('place_id')
                    <p class="text-sm text-black">{{ $message }}</p>
                @enderror
                <div id="place_results" class="hidden rounded border border-gray-300 bg-white"></div>
            </div>

            <div class="space-y-1">
                <label for="name" class="block text-sm font-medium text-black">Име</label>
                <input id="name" name="name" type="text" value="{{ old('name', $entity->name) }}" required maxlength="255" class="w-full rounded border border-gray-400 px-3 py-2 text-sm text-black">
            </div>

            <div class="space-y-1">
                <label for="address" class="block text-sm font-medium text-black">Адрес</label>
                <input id="address" name="address" type="text" value="{{ old('address', $entity->address) }}" maxlength="255" class="w-full rounded border border-gray-400 px-3 py-2 text-sm text-black">
            </div>

            <div class="space-y-1">
                <label for="phone" class="block text-sm font-medium text-black">Телефон</label>
                <input id="phone" name="phone" type="text" value="{{ old('phone', $entity->phone) }}" maxlength="255" class="w-full rounded border border-gray-400 px-3 py-2 text-sm text-black">
            </div>

            <div class="space-y-1">
                <label for="email" class="block text-sm font-medium text-black">Имейл</label>
                <input id="email" name="email" type="email" value="{{ old('email', $entity->email) }}" maxlength="255" class="w-full rounded border border-gray-400 px-3 py-2 text-sm text-black">
            </div>

            <div class="space-y-1">
                <label for="website" class="block text-sm font-medium text-black">Уебсайт</label>
                <input id="website" name="website" type="url" value="{{ old('website', $entity->website) }}" maxlength="255" class="w-full rounded border border-gray-400 px-3 py-2 text-sm text-black">
            </div>

            @if ($entityType->code === 'accommodation')
                <div class="space-y-1">
                    <label for="classification" class="block text-sm font-medium text-black">Категоризация</label>
                    <select id="classification" name="classification" class="w-full rounded border border-gray-400 px-3 py-2 text-sm text-black">
                        <option value="">Без категория</option>
                        <option value="1 звезда" @selected(old('classification', $entity->classification) === '1 звезда')>1 звезда</option>
                        <option value="2 звезди" @selected(old('classification', $entity->classification) === '2 звезди')>2 звезди</option>
                        <option value="3 звезди" @selected(old('classification', $entity->classification) === '3 звезди')>3 звезди</option>
                        <option value="4 звезди" @selected(old('classification', $entity->classification) === '4 звезди')>4 звезди</option>
                        <option value="5 звезди" @selected(old('classification', $entity->classification) === '5 звезди')>5 звезди</option>
                    </select>
                    @error('classification')
                        <p class="text-sm text-black">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            <div class="pt-2">
                <button type="submit" class="rounded border border-gray-400 px-4 py-2 text-sm text-black hover:bg-gray-100">
                    Запази промените
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        $(function () {
            var $country = $('#country_id');
            var $placeSearch = $('#place_search');
            var $placeId = $('#place_id');
            var $results = $('#place_results');
            var searchUrl = @json(route('admin.places.search'));
            var activeRequest = null;
            var selectedLabel = $placeSearch.val();

            function escapeHtml(text) {
                return $('<div>').text(text || '').html();
            }

            function renderLabel(place) {
                var parts = [];
                if (place.type) parts.push(place.type);
                if (place.municipality_name) parts.push(place.municipality_name);
                if (place.region_name) parts.push(place.region_name);
                return place.name + (parts.length ? ' (' + parts.join(', ') + ')' : '');
            }

            function hideResults() {
                $results.empty().addClass('hidden');
            }

            function clearSelectionIfChanged() {
                if ($placeSearch.val() !== selectedLabel) {
                    $placeId.val('');
                }
            }

            function searchPlaces() {
                var q = $.trim($placeSearch.val());
                var countryId = $country.val();

                if (q.length < 2) {
                    hideResults();
                    return;
                }

                if (activeRequest) {
                    activeRequest.abort();
                }

                activeRequest = $.get(searchUrl, {
                    q: q,
                    country_id: countryId
                }).done(function (items) {
                    $results.empty();
                    if (!items.length) {
                        $results
                            .append('<div class="px-3 py-2 text-sm text-gray-600">Няма резултати.</div>')
                            .removeClass('hidden');
                        return;
                    }

                    $.each(items, function (_, place) {
                        var label = renderLabel(place);
                        var $item = $('<button type="button" class="block w-full border-b border-gray-200 px-3 py-2 text-left text-sm text-black last:border-b-0 hover:bg-gray-100"></button>');
                        $item.html(escapeHtml(label));
                        $item.on('click', function () {
                            $placeId.val(place.id);
                            $placeSearch.val(label);
                            selectedLabel = label;
                            hideResults();
                        });
                        $results.append($item);
                    });

                    $results.removeClass('hidden');
                }).always(function () {
                    activeRequest = null;
                });
            }

            $placeSearch.on('input', function () {
                clearSelectionIfChanged();
                searchPlaces();
            });

            $country.on('change', function () {
                clearSelectionIfChanged();
                searchPlaces();
            });

            $(document).on('click', function (event) {
                if (!$(event.target).closest('#place_search, #place_results').length) {
                    hideResults();
                }
            });
        });
    </script>
@endpush

