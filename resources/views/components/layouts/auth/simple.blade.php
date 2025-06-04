<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
    <div class="bg-background flex min-h-svh flex-col items-center justify-center gap-2 p-4 md:p-6">
        <div class="flex w-full max-w-md sm:max-w-lg md:max-w-2xl lg:max-w-3xl flex-col gap-1">
            <a href="{{ route('home') }}" class="flex flex-col items-center gap-1 font-medium" wire:navigate>
                <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
            </a>
            <div class="flex flex-col gap-2">
                {{ $slot }}
            </div>
        </div>
    </div>
    @fluxScripts
</body>

</html>