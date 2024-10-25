<!doctype html>
<html lang="en">
<head>
    <title>{{ $item->title }}</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen">
<x-menu class="mb-8"/>

<main class="mx-auto max-w-2xl px-4 sm:px-6 py-12">
    <article class="prose lg:prose-xl">
        @if($item->hasImage('cover'))
            <div class="mb-8 rounded-lg overflow-hidden">
                <img
                    src="{{ $item->image('cover', 'default') }}"
                    alt="{{ $item->imageAltText('cover') }}"
                    class="w-full h-auto object-cover"
                />
                @if($item->imageCaption('cover'))
                    <p class="text-sm text-gray-500 mt-2 italic">
                        {{ $item->imageCaption('cover') }}
                    </p>
                @endif
            </div>
        @endif

        <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold tracking-tight mb-6">
            {{ $item->title }}
        </h1>

        <p class="text-lg text-gray-600 mb-8">
            <strong>description</strong><br /> {{ $item->description }}
        </p>

        <hr class="my-8 border-gray-200" />

        <div class="mt-8">
            {!! $item->renderBlocks() !!}
        </div>
    </article>
</main>
</body>
</html>
