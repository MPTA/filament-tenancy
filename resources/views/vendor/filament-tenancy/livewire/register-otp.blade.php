<div class="fi-simple-page">
    <section class="grid auto-cols-fr gap-y-6">
        <div class="text-center">
            <h2 class="text-xl font-bold tracking-tight">
                {{ $this->getHeading() }}
            </h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Please check our <x-filament::link target="_blank" href="https://discord.gg/vKV9U7gD3c">discord server</x-filament::link> and check <b>#otp</b> channel for the OTP.
            </p>
        </div>

        <form wire:submit.prevent="authenticate">
            {{ $this->form }}

            {{ $this->submitAction }}
        </form>

        <div class="text-center">
            <span class="text-gray-400">Don't get the code? please {{ $this->getResendAction }}</span>
            <x-filament-actions::modals />
        </div>
    </section>
</div>
