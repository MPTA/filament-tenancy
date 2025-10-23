<div class="min-h-screen bg-white dark:bg-gray-900">
    <!-- Navbar -->
    <nav class="bg-white border-b border-gray-200 dark:bg-gray-900 dark:border-gray-700 fixed w-full z-50 top-0">
        <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
            <a href="/" class="flex items-center space-x-3 rtl:space-x-reverse">
                <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path>
                </svg>
                <span class="self-center text-2xl font-bold whitespace-nowrap dark:text-white">MPTA</span>
            </a>
            <div class="flex items-center md:order-2 space-x-3 md:space-x-0 rtl:space-x-reverse">
                <!-- Dark Mode Toggle -->
                <button id="theme-toggle" type="button" class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5 me-3">
                    <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                    <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
                </button>
                
                @auth
                    <a href="/app" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Dashboard</a>
                @else
                    <a href="/admin/login" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 me-2 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">Sign In</a>
                    <a href="#partner" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Become a Partner</a>
                @endauth
            </div>
            <div class="items-center justify-between hidden w-full md:flex md:w-auto md:order-1">
                <ul class="flex flex-col font-medium p-4 md:p-0 mt-4 border border-gray-100 rounded-lg bg-gray-50 md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-white dark:bg-gray-800 md:dark:bg-gray-900 dark:border-gray-700">
                    <li><a href="#features" class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">Features</a></li>
                    <li><a href="#benefits" class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">Benefits</a></li>
                    <li><a href="#roadmap" class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">Roadmap</a></li>
                    <li><a href="#partner" class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">Partner Program</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="bg-white dark:bg-gray-900 mt-16">
        <div class="max-w-screen-xl px-4 pt-8 mx-auto text-center lg:pt-16 lg:px-12">
            <a href="#partner" class="inline-flex justify-between items-center py-1 px-1 pe-4 mb-7 text-sm text-blue-700 bg-blue-100 rounded-full dark:bg-blue-900 dark:text-blue-300 hover:bg-blue-200 dark:hover:bg-blue-800">
                <span class="text-xs bg-blue-600 rounded-full text-white px-4 py-1.5 me-3">Limited Offer</span> 
                <span class="text-sm font-medium">Free for 1 Year - First 10 DMC Agencies Only</span>
                <svg class="w-2.5 h-2.5 ms-2 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                </svg>
            </a>
            <h1 class="mb-4 text-4xl font-extrabold tracking-tight leading-none text-gray-900 md:text-5xl lg:text-6xl dark:text-white">Professional Quotation System for DMC Agencies</h1>
            <p class="mb-8 font-light text-gray-500 md:text-lg lg:text-xl sm:px-16 xl:px-48 dark:text-gray-400">Create detailed, competitive quotations in minutes instead of hours. Manage costs precisely, offer multiple options, and deliver professional proposals that win more clients.</p>
            <div class="flex flex-col mb-8 lg:mb-16 space-y-4 sm:flex-row sm:justify-center sm:space-y-0 sm:space-x-4">
                <a href="#partner" class="inline-flex items-center justify-center px-5 py-3 text-base font-medium text-center text-white rounded-lg bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-900">
                    Apply for Partnership
                    <svg class="w-5 h-5 ms-2 -me-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </a>
                <a href="#features" class="inline-flex items-center justify-center px-5 py-3 text-base font-medium text-center text-gray-900 border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 dark:text-white dark:border-gray-700 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
                    Explore Features
                    <svg class="w-5 h-5 ms-2 -me-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </a>
            </div>
            <!-- Dashboard Screenshot -->
            <img class="mx-auto mb-5 lg:mb-8 border border-gray-200 rounded-lg shadow-xl dark:border-gray-600" src="{{ asset('assets/images/dashboard-overview.png') }}" alt="TripMaker Dashboard Overview">
        </div>
        <!-- Problem Statement Section -->
        <div class="pt-48 lg:pb-16 pb-8 -mt-48 bg-gray-50 dark:bg-gray-800">
            <div class="px-4 mx-auto text-center md:max-w-screen-md lg:max-w-screen-lg lg:px-36">
                <span class="font-semibold text-gray-500 uppercase dark:text-gray-400">Designed specifically for DMC agencies</span>
                <div class="flex flex-wrap items-center justify-center mt-8 text-gray-500 dark:text-gray-400">
                    <div class="text-center mb-5 lg:mb-0">
                        <p class="text-lg font-medium mb-4">Stop losing time and money on manual quotations</p>
                        <p class="text-sm">When you calculate tour costs broadly, prices get inflated. TripMaker helps you focus on details - side costs, driver fees, leader expenses - to create competitive, profitable pricing.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="bg-white dark:bg-gray-900">
        <div class="py-8 px-4 mx-auto max-w-screen-xl sm:py-16 lg:px-6">
            <div class="max-w-screen-md mb-8 lg:mb-16">
                <h2 class="mb-4 text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white">Powerful Features for DMC Operations</h2>
                <p class="text-gray-500 sm:text-xl dark:text-gray-400">Everything you need to create winning quotations faster</p>
            </div>
            <div class="space-y-8 md:grid md:grid-cols-2 lg:grid-cols-3 md:gap-12 md:space-y-0">
                <!-- Feature 1 -->
                <div>
                    <div class="flex justify-center items-center mb-4 w-10 h-10 rounded-full bg-blue-100 lg:h-12 lg:w-12 dark:bg-blue-900">
                        <svg class="w-5 h-5 text-blue-600 lg:w-6 lg:h-6 dark:text-blue-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path></svg>
                    </div>
                    <h3 class="mb-2 text-xl font-bold dark:text-white">Dynamic Multi-Variable Pricing</h3>
                    <p class="text-gray-500 dark:text-gray-400">Instantly generate prices with different passenger counts, vehicle types, guide categories, currencies, and markup levels. Compare scenarios side-by-side.</p>
                </div>
                <!-- Feature 2 -->
                <div>
                    <div class="flex justify-center items-center mb-4 w-10 h-10 rounded-full bg-blue-100 lg:h-12 lg:w-12 dark:bg-blue-900">
                        <svg class="w-5 h-5 text-blue-600 lg:w-6 lg:h-6 dark:text-blue-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd"></path></svg>
                    </div>
                    <h3 class="mb-2 text-xl font-bold dark:text-white">Granular Cost Control</h3>
                    <p class="text-gray-500 dark:text-gray-400">Manage every detail: driver costs, tour leader fees, additional expenses, side costs. Optimize each element to achieve competitive pricing without sacrificing profit.</p>
                </div>
                <!-- Feature 3 -->
                <div>
                    <div class="flex justify-center items-center mb-4 w-10 h-10 rounded-full bg-blue-100 lg:h-12 lg:w-12 dark:bg-blue-900">
                        <svg class="w-5 h-5 text-blue-600 lg:w-6 lg:h-6 dark:text-blue-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path></svg>
                    </div>
                    <h3 class="mb-2 text-xl font-bold dark:text-white">Professional Quotation Output</h3>
                    <p class="text-gray-500 dark:text-gray-400">Deliver beautiful, detailed quotations that showcase your professionalism. Include full itinerary, pricing breakdown, and travel details in client's language.</p>
                </div>
                <!-- Feature 4 -->
                <div>
                    <div class="flex justify-center items-center mb-4 w-10 h-10 rounded-full bg-blue-100 lg:h-12 lg:w-12 dark:bg-blue-900">
                        <svg class="w-5 h-5 text-blue-600 lg:w-6 lg:h-6 dark:text-blue-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7 2a1 1 0 00-.707 1.707L7 4.414v3.758a1 1 0 01-.293.707l-4 4C.817 14.769 2.156 18 4.828 18h10.343c2.673 0 4.012-3.231 2.122-5.121l-4-4A1 1 0 0113 8.172V4.414l.707-.707A1 1 0 0013 2H7zm2 6.172V4h2v4.172a3 3 0 00.879 2.12l1.027 1.028a4 4 0 00-2.171.102l-.47.156a4 4 0 01-2.53 0l-.563-.187a1.993 1.993 0 00-.114-.035l1.063-1.063A3 3 0 009 8.172z" clip-rule="evenodd"></path></svg>
                    </div>
                    <h3 class="mb-2 text-xl font-bold dark:text-white">Multi-Language & Multi-Currency</h3>
                    <p class="text-gray-500 dark:text-gray-400">Serve international clients seamlessly. Generate quotations in their language and currency. Built-in translation and real-time exchange rate management.</p>
                </div>
                <!-- Feature 5 -->
                <div>
                    <div class="flex justify-center items-center mb-4 w-10 h-10 rounded-full bg-blue-100 lg:h-12 lg:w-12 dark:bg-blue-900">
                        <svg class="w-5 h-5 text-blue-600 lg:w-6 lg:h-6 dark:text-blue-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M3 5a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2h-2.22l.123.489.804.804A1 1 0 0113 18H7a1 1 0 01-.707-1.707l.804-.804L7.22 15H5a2 2 0 01-2-2V5zm5.771 7H5V5h10v7H8.771z" clip-rule="evenodd"></path></svg>
                    </div>
                    <h3 class="mb-2 text-xl font-bold dark:text-white">Centralized Master Data</h3>
                    <p class="text-gray-500 dark:text-gray-400">Access pre-loaded database of hotels, attractions, cities, and countries. No need to enter basic data - just customize with your rates and services.</p>
                </div>
                <!-- Feature 6 -->
                <div>
                    <div class="flex justify-center items-center mb-4 w-10 h-10 rounded-full bg-blue-100 lg:h-12 lg:w-12 dark:bg-blue-900">
                        <svg class="w-5 h-5 text-blue-600 lg:w-6 lg:h-6 dark:text-blue-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"></path></svg>
                    </div>
                    <h3 class="mb-2 text-xl font-bold dark:text-white">Customer & Inquiry Management</h3>
                    <p class="text-gray-500 dark:text-gray-400">Track inquiries, manage customer information, store documents and attachments. Complete history of interactions and quotations in one place.</p>
                </div>
                <!-- Feature 7 -->
                <div>
                    <div class="flex justify-center items-center mb-4 w-10 h-10 rounded-full bg-blue-100 lg:h-12 lg:w-12 dark:bg-blue-900">
                        <svg class="w-5 h-5 text-blue-600 lg:w-6 lg:h-6 dark:text-blue-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path></svg>
                    </div>
                    <h3 class="mb-2 text-xl font-bold dark:text-white">Custom Experiences & Events</h3>
                    <p class="text-gray-500 dark:text-gray-400">Define your unique tours, special events, and exclusive experiences. Build a library of signature offerings that differentiate your agency.</p>
                </div>
                <!-- Feature 8 -->
                <div>
                    <div class="flex justify-center items-center mb-4 w-10 h-10 rounded-full bg-blue-100 lg:h-12 lg:w-12 dark:bg-blue-900">
                        <svg class="w-5 h-5 text-blue-600 lg:w-6 lg:h-6 dark:text-blue-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"></path></svg>
                    </div>
                    <h3 class="mb-2 text-xl font-bold dark:text-white">Detailed Itinerary Builder</h3>
                    <p class="text-gray-500 dark:text-gray-400">Create day-by-day travel programs with precision. Include hotels, meals, activities, transportation, and attractions. Export in any language.</p>
                </div>
                <!-- Feature 9 -->
                <div>
                    <div class="flex justify-center items-center mb-4 w-10 h-10 rounded-full bg-blue-100 lg:h-12 lg:w-12 dark:bg-blue-900">
                        <svg class="w-5 h-5 text-blue-600 lg:w-6 lg:h-6 dark:text-blue-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path></svg>
                    </div>
                    <h3 class="mb-2 text-xl font-bold dark:text-white">Speed & Efficiency</h3>
                    <p class="text-gray-500 dark:text-gray-400">What used to take hours now takes minutes. Respond to inquiries faster, create more quotations, and win more business with less effort.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section id="benefits" class="bg-gray-50 dark:bg-gray-800">
        <div class="py-8 px-4 mx-auto max-w-screen-xl sm:py-16 lg:px-6">
            <div class="mb-8 max-w-screen-md lg:mb-16">
                <h2 class="mb-4 text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white">Why DMC Agencies Choose TripMaker</h2>
                <p class="text-gray-500 sm:text-xl dark:text-gray-400">Transform how you do business</p>
            </div>
            <div class="space-y-8 md:grid md:grid-cols-2 lg:grid-cols-3 md:gap-12 md:space-y-0">
                <div class="flex flex-col items-center text-center">
                    <div class="flex justify-center items-center w-16 h-16 mb-4 bg-blue-100 rounded-full dark:bg-blue-900">
                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    </div>
                    <h3 class="mb-2 text-xl font-bold dark:text-white">Competitive Pricing</h3>
                    <p class="text-gray-500 dark:text-gray-400">Win more bids by managing costs precisely. Lower your quotes without cutting profit.</p>
                </div>
                <div class="flex flex-col items-center text-center">
                    <div class="flex justify-center items-center w-16 h-16 mb-4 bg-blue-100 rounded-full dark:bg-blue-900">
                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    </div>
                    <h3 class="mb-2 text-xl font-bold dark:text-white">Professional Image</h3>
                    <p class="text-gray-500 dark:text-gray-400">Impress clients with detailed, beautifully formatted proposals that demonstrate expertise.</p>
                </div>
                <div class="flex flex-col items-center text-center">
                    <div class="flex justify-center items-center w-16 h-16 mb-4 bg-blue-100 rounded-full dark:bg-blue-900">
                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    </div>
                    <h3 class="mb-2 text-xl font-bold dark:text-white">Save Time</h3>
                    <p class="text-gray-500 dark:text-gray-400">Reduce quotation prep time by 70%. Respond faster and handle more inquiries.</p>
                </div>
                <div class="flex flex-col items-center text-center">
                    <div class="flex justify-center items-center w-16 h-16 mb-4 bg-blue-100 rounded-full dark:bg-blue-900">
                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    </div>
                    <h3 class="mb-2 text-xl font-bold dark:text-white">Serve Global Markets</h3>
                    <p class="text-gray-500 dark:text-gray-400">Multi-language support helps you communicate with clients worldwide in their native language.</p>
                </div>
                <div class="flex flex-col items-center text-center">
                    <div class="flex justify-center items-center w-16 h-16 mb-4 bg-blue-100 rounded-full dark:bg-blue-900">
                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    </div>
                    <h3 class="mb-2 text-xl font-bold dark:text-white">Data Security</h3>
                    <p class="text-gray-500 dark:text-gray-400">Enterprise-grade security keeps your business data, customer information, and pricing safe.</p>
                </div>
                <div class="flex flex-col items-center text-center">
                    <div class="flex justify-center items-center w-16 h-16 mb-4 bg-blue-100 rounded-full dark:bg-blue-900">
                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    </div>
                    <h3 class="mb-2 text-xl font-bold dark:text-white">Team Collaboration</h3>
                    <p class="text-gray-500 dark:text-gray-400">Multiple team members can work together seamlessly. Define roles and access levels.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Roadmap Section -->
    <section id="roadmap" class="bg-white dark:bg-gray-900">
        <div class="py-8 px-4 mx-auto max-w-screen-xl sm:py-16 lg:px-6">
            <div class="max-w-screen-md mb-8 lg:mb-16">
                <h2 class="mb-4 text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white">Our Vision & Roadmap</h2>
                <p class="text-gray-500 sm:text-xl dark:text-gray-400">The current system is just the beginning. We're building a complete ecosystem for the travel industry.</p>
            </div>
            <div class="space-y-8">
                <!-- Phase 1 - Current -->
                <div class="flex">
                    <div class="flex flex-col items-center me-4">
                        <div class="flex items-center justify-center w-10 h-10 bg-green-100 rounded-full ring-4 ring-white dark:ring-gray-900 dark:bg-green-900 shrink-0">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        </div>
                        <div class="w-px h-full bg-gray-300 dark:bg-gray-700"></div>
                    </div>
                    <div class="pt-1 pb-8">
                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">CURRENT - Available Now</span>
                        <h3 class="mt-3 text-lg font-semibold text-gray-900 dark:text-white">Core Quotation & Itinerary System</h3>
                        <p class="text-gray-500 dark:text-gray-400 mt-1">Complete inquiry management, multi-variable pricing, professional quotations, detailed itineraries, customer management, and multi-language support.</p>
                    </div>
                </div>

                <!-- Phase 2 - AI -->
                <div class="flex">
                    <div class="flex flex-col items-center me-4">
                        <div class="flex items-center justify-center w-10 h-10 bg-blue-100 rounded-full ring-4 ring-white dark:ring-gray-900 dark:bg-blue-900 shrink-0">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"></path></svg>
                        </div>
                        <div class="w-px h-full bg-gray-300 dark:bg-gray-700"></div>
                    </div>
                    <div class="pt-1 pb-8">
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">PHASE 2 - In Development</span>
                        <h3 class="mt-3 text-lg font-semibold text-gray-900 dark:text-white">AI-Powered Itinerary Planning</h3>
                        <p class="text-gray-500 dark:text-gray-400 mt-1">Artificial intelligence will automatically generate detailed, optimized itineraries based on customer preferences, budget, and destination. Complete with attractions, timings, and logistics.</p>
                    </div>
                </div>

                <!-- Phase 3 - Marketplace -->
                <div class="flex">
                    <div class="flex flex-col items-center me-4">
                        <div class="flex items-center justify-center w-10 h-10 bg-blue-100 rounded-full ring-4 ring-white dark:ring-gray-900 dark:bg-blue-900 shrink-0">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"></path></svg>
                        </div>
                        <div class="w-px h-full bg-gray-300 dark:bg-gray-700"></div>
                    </div>
                    <div class="pt-1 pb-8">
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">PHASE 3 - Planned</span>
                        <h3 class="mt-3 text-lg font-semibold text-gray-900 dark:text-white">Global DMC Marketplace</h3>
                        <p class="text-gray-500 dark:text-gray-400 mt-1">Connect with international clients through our marketplace. Travelers worldwide will submit tour requests, and participating DMCs can bid. Example: Russian client wants a China tour, Chinese DMC agencies receive and quote the request.</p>
                    </div>
                </div>

                <!-- Phase 4 - Tour Management -->
                <div class="flex">
                    <div class="flex flex-col items-center me-4">
                        <div class="flex items-center justify-center w-10 h-10 bg-blue-100 rounded-full ring-4 ring-white dark:ring-gray-900 dark:bg-blue-900 shrink-0">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path></svg>
                        </div>
                        <div class="w-px h-full bg-gray-300 dark:bg-gray-700"></div>
                    </div>
                    <div class="pt-1 pb-8">
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">PHASE 4 - Planned</span>
                        <h3 class="mt-3 text-lg font-semibold text-gray-900 dark:text-white">Tour Operations Management</h3>
                        <p class="text-gray-500 dark:text-gray-400 mt-1">Manage active tours with budgeting tools, expense tracking, payment management, and reservation systems. Book hotels, vehicles, and services, and monitor costs in real-time.</p>
                    </div>
                </div>

                <!-- Phase 5 - Agency Website -->
                <div class="flex">
                    <div class="flex flex-col items-center me-4">
                        <div class="flex items-center justify-center w-10 h-10 bg-blue-100 rounded-full ring-4 ring-white dark:ring-gray-900 dark:bg-blue-900 shrink-0">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M3 5a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2h-2.22l.123.489.804.804A1 1 0 0113 18H7a1 1 0 01-.707-1.707l.804-.804L7.22 15H5a2 2 0 01-2-2V5zm5.771 7H5V5h10v7H8.771z" clip-rule="evenodd"></path></svg>
                        </div>
                        <div class="w-px h-full bg-gray-300 dark:bg-gray-700"></div>
                    </div>
                    <div class="pt-1 pb-8">
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">PHASE 5 - Planned</span>
                        <h3 class="mt-3 text-lg font-semibold text-gray-900 dark:text-white">Dedicated Agency Website</h3>
                        <p class="text-gray-500 dark:text-gray-400 mt-1">Each agency gets a customizable website to showcase itineraries publicly. Clients can submit quote requests via unique agency links and track their tour reservations directly.</p>
                    </div>
                </div>

                <!-- Phase 6 - Feedback System -->
                <div class="flex">
                    <div class="flex flex-col items-center me-4">
                        <div class="flex items-center justify-center w-10 h-10 bg-blue-100 rounded-full ring-4 ring-white dark:ring-gray-900 dark:bg-blue-900 shrink-0">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"></path><path d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z"></path></svg>
                        </div>
                        <div class="w-px h-full bg-gray-300 dark:bg-gray-700"></div>
                    </div>
                    <div class="pt-1 pb-8">
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">PHASE 6 - Planned</span>
                        <h3 class="mt-3 text-lg font-semibold text-gray-900 dark:text-white">Tour Leader Feedback Network</h3>
                        <p class="text-gray-500 dark:text-gray-400 mt-1">Mobile app for tour leaders to collect guest feedback, conduct surveys, and gather content during tours. Share insights with clients to create a collaborative travel community.</p>
                    </div>
                </div>

                <!-- Phase 7 - External APIs -->
                <div class="flex">
                    <div class="flex flex-col items-center me-4">
                        <div class="flex items-center justify-center w-10 h-10 bg-blue-100 rounded-full ring-4 ring-white dark:ring-gray-900 dark:bg-blue-900 shrink-0">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path></svg>
                        </div>
                        <div class="w-px h-full bg-gray-300 dark:bg-gray-700"></div>
                    </div>
                    <div class="pt-1 pb-8">
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">PHASE 7 - Planned</span>
                        <h3 class="mt-3 text-lg font-semibold text-gray-900 dark:text-white">External Service Integration</h3>
                        <p class="text-gray-500 dark:text-gray-400 mt-1">Connect with global booking platforms. Get real-time pricing and availability for hotels, flights, vehicles, and attractions. Book services directly through external APIs.</p>
                    </div>
                </div>

                <!-- Phase 8 - AI CRM -->
                <div class="flex">
                    <div class="flex flex-col items-center me-4">
                        <div class="flex items-center justify-center w-10 h-10 bg-blue-100 rounded-full ring-4 ring-white dark:ring-gray-900 dark:bg-blue-900 shrink-0">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M13 7H7v6h6V7z"></path><path fill-rule="evenodd" d="M7 2a1 1 0 012 0v1h2V2a1 1 0 112 0v1h2a2 2 0 012 2v2h1a1 1 0 110 2h-1v2h1a1 1 0 110 2h-1v2a2 2 0 01-2 2h-2v1a1 1 0 11-2 0v-1H9v1a1 1 0 11-2 0v-1H5a2 2 0 01-2-2v-2H2a1 1 0 110-2h1V9H2a1 1 0 010-2h1V5a2 2 0 012-2h2V2zM5 5h10v10H5V5z" clip-rule="evenodd"></path></svg>
                        </div>
                    </div>
                    <div class="pt-1">
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">PHASE 8 - Planned</span>
                        <h3 class="mt-3 text-lg font-semibold text-gray-900 dark:text-white">AI-Powered CRM & Analytics</h3>
                        <p class="text-gray-500 dark:text-gray-400 mt-1">Advanced customer relationship management with AI-driven insights, predictive analytics, automated follow-ups, and intelligent reporting to optimize your sales and service.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Partner Program Section -->
    <section id="partner" class="bg-gradient-to-r from-blue-700 to-blue-900 dark:from-blue-900 dark:to-gray-900">
        <div class="py-8 px-4 mx-auto max-w-screen-xl sm:py-16 lg:px-6">
            <div class="max-w-screen-md mb-8 lg:mb-16 mx-auto text-center">
                <h2 class="mb-4 text-4xl tracking-tight font-extrabold text-white">Limited Partnership Opportunity</h2>
                <p class="text-blue-100 sm:text-xl">Be among the first 10 DMC agencies to join our partner network</p>
            </div>
            
            <div class="max-w-screen-lg mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-xl p-8 md:p-12">
                <div class="grid md:grid-cols-2 gap-8 mb-8">
                    <div>
                        <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Why Partner With Us?</h3>
                        <ul class="space-y-3">
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-green-500 mt-1 me-2 shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                <span class="text-gray-700 dark:text-gray-300"><strong>1 Year Free:</strong> Complete access to all current features at no cost</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-green-500 mt-1 me-2 shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                <span class="text-gray-700 dark:text-gray-300"><strong>Shape the Product:</strong> Your feedback directly influences development</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-green-500 mt-1 me-2 shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                <span class="text-gray-700 dark:text-gray-300"><strong>Priority Access:</strong> First to receive all new features and updates</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-green-500 mt-1 me-2 shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                <span class="text-gray-700 dark:text-gray-300"><strong>Dedicated Support:</strong> Direct communication with our development team</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-green-500 mt-1 me-2 shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                <span class="text-gray-700 dark:text-gray-300"><strong>Future Marketplace:</strong> Founding members get premium positioning</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-green-500 mt-1 me-2 shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                <span class="text-gray-700 dark:text-gray-300"><strong>Network Effect:</strong> Join a growing network of professional DMCs</span>
                            </li>
                        </ul>
                    </div>
                    
                    <div>
                        <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Partner Application Form</h3>
                        <form class="space-y-4" action="#" method="POST">
                            <div>
                                <label for="company_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Company Name *</label>
                                <input type="text" id="company_name" name="company_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="ABC Travel Agency" required>
                            </div>
                            <div>
                                <label for="city" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">City *</label>
                                <input type="text" id="city" name="city" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Beijing" required>
                            </div>
                            <div>
                                <label for="country" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Country *</label>
                                <input type="text" id="country" name="country" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="China" required>
                            </div>
                            <div>
                                <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email Address *</label>
                                <input type="email" id="email" name="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="contact@example.com" required>
                            </div>
                            <button type="submit" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Submit Partnership Application</button>
                            <p class="text-xs text-gray-500 dark:text-gray-400 text-center">We'll contact you within 24 hours</p>
                        </form>
                    </div>
                </div>
                
                <div class="bg-amber-50 dark:bg-amber-900/20 border-l-4 border-amber-500 p-4 rounded">
                    <div class="flex">
                        <svg class="w-5 h-5 text-amber-500 mt-0.5 me-3 shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                        <div>
                            <h4 class="font-bold text-amber-800 dark:text-amber-300 mb-1">Limited Availability</h4>
                            <p class="text-sm text-amber-700 dark:text-amber-400">Only <strong>10 partnership slots</strong> available. Once filled, future agencies will need to subscribe at regular rates. The core system will always be free, but advanced features will be monetized through the marketplace network.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Vision Statement -->
    <section class="bg-gray-50 dark:bg-gray-800">
        <div class="py-8 px-4 mx-auto max-w-screen-xl sm:py-16 lg:px-6">
            <div class="max-w-screen-md mx-auto text-center">
                <h2 class="mb-4 text-3xl tracking-tight font-bold text-gray-900 dark:text-white">Our Mission</h2>
                <p class="text-gray-500 sm:text-lg dark:text-gray-400 mb-6">
                    We're building more than software—we're creating a global community for the travel industry. For each tour group, we envision a small, connected community where content, feedback, and experiences are shared among all stakeholders.
                </p>
                <p class="text-gray-500 sm:text-lg dark:text-gray-400 mb-6">
                    <strong>Our commitment:</strong> The core quotation and itinerary system will always be free. In the future, we'll monetize through our marketplace network and premium features, but foundational tools remain accessible to all DMC agencies.
                </p>
                <p class="text-blue-600 dark:text-blue-400 font-semibold text-lg">
                    Join us in transforming how the travel industry works together.
                </p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white dark:bg-gray-900">
        <div class="mx-auto w-full max-w-screen-xl p-4 py-6 lg:py-8">
            <div class="md:flex md:justify-between">
                <div class="mb-6 md:mb-0">
                    <a href="/" class="flex items-center">
                        <svg class="w-8 h-8 text-blue-600 me-3" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path>
                        </svg>
                        <span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white">MPTA</span>
                    </a>
                    <p class="mt-4 text-sm text-gray-500 dark:text-gray-400 max-w-xs">Professional quotation and itinerary system designed specifically for DMC agencies worldwide.</p>
                </div>
                <div class="grid grid-cols-2 gap-8 sm:gap-6 sm:grid-cols-3">
                    <div>
                        <h2 class="mb-6 text-sm font-semibold text-gray-900 uppercase dark:text-white">Product</h2>
                        <ul class="text-gray-500 dark:text-gray-400 font-medium">
                            <li class="mb-4"><a href="#features" class="hover:underline">Features</a></li>
                            <li class="mb-4"><a href="#benefits" class="hover:underline">Benefits</a></li>
                            <li><a href="#roadmap" class="hover:underline">Roadmap</a></li>
                        </ul>
                    </div>
                    <div>
                        <h2 class="mb-6 text-sm font-semibold text-gray-900 uppercase dark:text-white">Partnership</h2>
                        <ul class="text-gray-500 dark:text-gray-400 font-medium">
                            <li class="mb-4"><a href="#partner" class="hover:underline">Become a Partner</a></li>
                            <li class="mb-4"><a href="#" class="hover:underline">Partner Benefits</a></li>
                            <li><a href="#" class="hover:underline">Contact Us</a></li>
                        </ul>
                    </div>
                    <div>
                        <h2 class="mb-6 text-sm font-semibold text-gray-900 uppercase dark:text-white">Legal</h2>
                        <ul class="text-gray-500 dark:text-gray-400 font-medium">
                            <li class="mb-4"><a href="#" class="hover:underline">Privacy Policy</a></li>
                            <li><a href="#" class="hover:underline">Terms of Service</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <hr class="my-6 border-gray-200 sm:mx-auto dark:border-gray-700 lg:my-8" />
            <div class="sm:flex sm:items-center sm:justify-between">
                <span class="text-sm text-gray-500 sm:text-center dark:text-gray-400">© {{ date('Y') }} <a href="/" class="hover:underline">MPTA</a>. All Rights Reserved.</span>
                <div class="flex mt-4 sm:justify-center sm:mt-0">
                    <a href="#" class="text-gray-500 hover:text-gray-900 dark:hover:text-white">
                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 8 19">
                            <path fill-rule="evenodd" d="M6.135 3H8V0H6.135a4.147 4.147 0 0 0-4.142 4.142V6H0v3h2v9.938h3V9h2.021l.592-3H5V3.591A.6.6 0 0 1 5.592 3h.543Z" clip-rule="evenodd"/>
                        </svg>
                        <span class="sr-only">Facebook page</span>
                    </a>
                    <a href="#" class="text-gray-500 hover:text-gray-900 dark:hover:text-white ms-5">
                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 17">
                            <path fill-rule="evenodd" d="M20 1.892a8.178 8.178 0 0 1-2.355.635 4.074 4.074 0 0 0 1.8-2.235 8.344 8.344 0 0 1-2.605.98A4.13 4.13 0 0 0 13.85 0a4.068 4.068 0 0 0-4.1 4.038 4 4 0 0 0 .105.919A11.705 11.705 0 0 1 1.4.734a4.006 4.006 0 0 0 1.268 5.392 4.165 4.165 0 0 1-1.859-.5v.05A4.057 4.057 0 0 0 4.1 9.635a4.19 4.19 0 0 1-1.856.07 4.108 4.108 0 0 0 3.831 2.807A8.36 8.36 0 0 1 0 14.184 11.732 11.732 0 0 0 6.291 16 11.502 11.502 0 0 0 17.964 4.5c0-.177 0-.35-.012-.523A8.143 8.143 0 0 0 20 1.892Z" clip-rule="evenodd"/>
                        </svg>
                        <span class="sr-only">Twitter page</span>
                    </a>
                    <a href="#" class="text-gray-500 hover:text-gray-900 dark:hover:text-white ms-5">
                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 0a10 10 0 1 0 10 10A10.009 10.009 0 0 0 10 0Zm6.613 4.614a8.523 8.523 0 0 1 1.93 5.32 20.094 20.094 0 0 0-5.949-.274c-.059-.149-.122-.292-.184-.441a23.879 23.879 0 0 0-.566-1.239 11.41 11.41 0 0 0 4.769-3.366ZM8 1.707a8.821 8.821 0 0 1 2-.238 8.5 8.5 0 0 1 5.664 2.152 9.608 9.608 0 0 1-4.476 3.087A45.758 45.758 0 0 0 8 1.707ZM1.642 8.262a8.57 8.57 0 0 1 4.73-5.981A53.998 53.998 0 0 1 9.54 7.222a32.078 32.078 0 0 1-7.9 1.04h.002Zm2.01 7.46a8.51 8.51 0 0 1-2.2-5.707v-.262a31.64 31.64 0 0 0 8.777-1.219c.243.477.477.964.692 1.449-.114.032-.227.067-.336.1a13.569 13.569 0 0 0-6.942 5.636l.009.003ZM10 18.556a8.508 8.508 0 0 1-5.243-1.8 11.717 11.717 0 0 1 6.7-5.332.509.509 0 0 1 .055-.02 35.65 35.65 0 0 1 1.819 6.476 8.476 8.476 0 0 1-3.331.676Zm4.772-1.462A37.232 37.232 0 0 0 13.113 11a12.513 12.513 0 0 1 5.321.364 8.56 8.56 0 0 1-3.66 5.73h-.002Z" clip-rule="evenodd"/>
                        </svg>
                        <span class="sr-only">Dribbble account</span>
                    </a>
                </div>
            </div>
        </div>
    </footer>
</div>
