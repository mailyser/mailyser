<x-filament::card class="text-center py-12">
    <x-filament::card.heading>
        <span class="mr-2 inline-flex items-center justify-center h-8 w-8 bg-primary-600 text-white rounded-full">
            1
        </span>
        Download the audience
    </x-filament::card.heading>

    <div>
        <p class="mb-8 max-w-xl mx-auto">
            First of all, download the audience file. This CSV file contains the information of the email accounts where you will need to send the newsletter to.
        </p>

        <x-filament::button wire:click.prevent="downloadContacts">
            <span class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Download Contacts
            </span>
        </x-filament::button>
        
         <p class="mb-8 max-w-xl mx-auto" style="margin-bottom: 0px; margin-top: 2rem;border: 1px solid #DEDEDE;border-radius: 25px;padding: 20px;">
            <svg style="position: relative; top: -10px; left: -10px;" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" height="20px" width="20px" version="1.1" viewBox="0 0 512 512" enable-background="new 0 0 512 512">
              <g>
                <g fill="#231F20">
                  <path d="m363.9,54.2c-31.2-29.8-72.3-45.1-115.4-43-77.6,3.6-141.3,64.6-148.1,141.9-3.7,41.3 8.8,81.6 35,113.4 26.4,32.1 48,89 48,126.9 0,0-2.1,13.8 10.4,14.4h124.7c13.6-1 10.4-17 10.4-17 0-37 21.1-92.3 47.1-123.3 23.5-28 36.4-63.6 36.4-100.1-0.1-43.2-17.3-83.4-48.5-113.2zm-116.1,332.7l-24-145.9h64.5l-24,145.9h-16.5zm112.1-132.9c-28.3,33.7-50.6,91.2-51.9,132.8h-22.6l25.5-154.6c0.7-3.2-0.7-11.6-10.3-12.1h-89c-9.2,0.3-11,9-10.3,12.1l25.5,154.6h-22.7c-2.1-42.3-24.5-99.6-52.6-133.7-22.7-27.5-33.5-62.4-30.3-98.3 5.8-66.8 61-119.6 128.2-122.8 37.5-1.8 73,11.5 100,37.3 27.1,25.8 42,60.6 42,98 0.1,31.7-11.1,62.5-31.5,86.7z"></path>
                  <path d="m319.7,433.9h-127.4c-5.8,0-10.4,4.7-10.4,10.4 0,5.8 4.7,10.4 10.4,10.4h9.7c4.1,26.1 26.7,46.2 54,46.2 27.3,0 49.9-20.1 54-46.2h9.7c5.8,0 10.4-4.7 10.4-10.4 5.68434e-14-5.7-4.6-10.4-10.4-10.4zm-63.7,46.2c-15,0-27.7-9.8-32.1-23.3h64.2c-4.4,13.5-17.1,23.3-32.1,23.3z"></path>
                </g>
              </g>
            </svg>
You can send a minimum of 10 emails from this list (must be the first 10 from the top). The more emails you send - the more accurate your response will be as you will be using the entire distributed network.
        </p>

    </div>
</x-filament::card>
