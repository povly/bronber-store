@push('block-styles')
    @vite(['resources/css/blocks/about/style.css'])
@endpush

@push('block-scripts')
    @vite(['resources/js/blocks/about/index.js'])
@endpush

@php
    $timeline = [
        [
            'year' => '2017',
            'title' => __('about.year_2017_title'),
            'text' => __('about.year_2017_text'),
        ],
        [
            'year' => '2018',
            'title' => __('about.year_2018_title'),
            'text' => __('about.year_2018_text'),
        ],
        [
            'year' => '2019',
            'title' => __('about.year_2019_title'),
            'text' => __('about.year_2019_text'),
        ],
        [
            'year' => '2020',
            'title' => __('about.year_2020_title'),
            'text' => __('about.year_2020_text'),
        ],
        [
            'year' => '2021',
            'title' => __('about.year_2021_title'),
            'text' => __('about.year_2021_text'),
        ],
    ];
@endphp

<section class="about" x-data="about()">
    <div class="container">
        <h1 class="about__title section__title">{{ __('about.title') }}</h1>

        <div class="about__desktop">
            <x-slider :config="['breakpoints' => [0 => ['perView' => 2], 768 => ['perView' => 3], 1200 => ['perView' => 5]]]" viewport-class="about__years" track-class="about__years-track"
                label="История компании">
                @foreach ($timeline as $i => $item)
                    <div class="about__year-slide slider__slide"
                        :class="{ 'is-active': active === {{ $i }} }">
                        <span class="about__year-marker">
                            <svg width="27" height="23" viewBox="0 0 27 23" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M13.4238 22.5L0.000434935 -6.21215e-07L26.8472 1.72581e-06L13.4238 22.5Z"
                                    fill="black" />
                            </svg>
                        </span>
                        <button type="button" class="about__year"
                            @click="select({{ $i }}); $nextTick(() => scrollToReveal({{ $i }}))">
                            {{ $item['year'] }}
                        </button>
                    </div>
                @endforeach
            </x-slider>

            <div class="about__separator"></div>

            <div class="about__panels">
                @foreach ($timeline as $i => $item)
                    <div class="about__panel" x-show="active === {{ $i }}" x-collapse>
                        <h2 class="about__subtitle section__title">{{ $item['title'] }}</h2>
                        <div class="about__text">
                            <p>PASHA Holding invites experienced candidates to apply for the position of Audit Manager
                                within the Group Audit Department. The Audit Manager will play a critical role in
                                formulating and executing the Group Audit Strategy, ensuring the integrity and
                                effectiveness of our internal audit processes.</p>
                            <p>Job description</p>
                            <ul>
                                <li>Participate in the formulation and execution of a three-year Group Audit Strategy,
                                    ensuring alignment across financial sector companies.</li>
                                <li>Establish and monitor the implementation of unified audit policies and procedures
                                    for Internal Audit Departments (IADs) within Strategic Assets.</li>
                                <li>Manage and execute the Quality Assurance and Improvement Program (QAIP) for internal
                                    audits, including both internal and external assessments.</li>
                                <li>Assist Audit Committees in compliance activities, including preparation of charters
                                    and agendas, and regular reporting (internal and external).</li>
                                <li>Review and confirm the accuracy and timeliness of audit engagement reports by IADs
                                    of Strategic Assets, and oversee annual audit plans and findings.</li>
                                <li>Facilitate collaboration between internal and external audit functions to minimize
                                    duplication and enhance coverage.</li>
                                <li>Conduct audit engagements in Strategic Assets;</li>
                                <li>Provide training and support to internal auditors to enhance their skills and
                                    knowledge.</li>
                            </ul>
                            <p>Experience, Competencies and Skills Required:</p>
                            <ul>
                                <li>Minimum of 5 years of total work experience, with at least 3 years in an audit
                                    function in the financial sector.</li>
                                <li>Graduate degree in a relevant field required.</li>
                                <li>Professional certifications such as CIA or ACCA are highly desirable.</li>
                                <li>Fluency in English is required; proficiency in Russian and Turkish is preferred.
                                </li>
                                <li>Proficient in MS Office; familiarity with data analytics and automated audit tools
                                    is a plus.</li>
                                <li>Strong analytical, communication, and conflict management skills.</li>
                            </ul>
                        </div>
                        <div class="about__image-wrap img--full">
                            <x-img path="/images/about/bg.jpg" width="1100" height="382" :alt="$item['title']"
                                class="about__image" />
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="about__accordion">
            <div class="about__acc-separator"></div>
            @foreach ($timeline as $i => $item)
                <div class="about__acc-item" :class="{ 'is-open': accActive === {{ $i }} }">
                    <button type="button" class="about__acc-header" @click="accToggle({{ $i }})"
                        :aria-expanded="accActive === {{ $i }}">
                        <span class="about__acc-title">{{ $item['year'] }} — {{ $item['title'] }}</span>
                        <span class="about__acc-chevron">
                            <span></span>
                            <span></span>
                        </span>
                    </button>
                    <div class="about__acc-content" x-show="accActive === {{ $i }}" x-collapse
                        @if ($i !== 0) x-cloak @endif>
                        <div class="about__text">
                            <p>PASHA Holding invites experienced candidates to apply for the position of Audit Manager
                                within the Group Audit Department. The Audit Manager will play a critical role in
                                formulating and executing the Group Audit Strategy, ensuring the integrity and
                                effectiveness of our internal audit processes.</p>
                            <p>Job description</p>
                            <ul>
                                <li>Participate in the formulation and execution of a three-year Group Audit Strategy,
                                    ensuring alignment across financial sector companies.</li>
                                <li>Establish and monitor the implementation of unified audit policies and procedures
                                    for Internal Audit Departments (IADs) within Strategic Assets.</li>
                                <li>Manage and execute the Quality Assurance and Improvement Program (QAIP) for internal
                                    audits, including both internal and external assessments.</li>
                                <li>Assist Audit Committees in compliance activities, including preparation of charters
                                    and agendas, and regular reporting (internal and external).</li>
                                <li>Review and confirm the accuracy and timeliness of audit engagement reports by IADs
                                    of Strategic Assets, and oversee annual audit plans and findings.</li>
                                <li>Facilitate collaboration between internal and external audit functions to minimize
                                    duplication and enhance coverage.</li>
                                <li>Conduct audit engagements in Strategic Assets;</li>
                                <li>Provide training and support to internal auditors to enhance their skills and
                                    knowledge.</li>
                            </ul>
                            <p>Experience, Competencies and Skills Required:</p>
                            <ul>
                                <li>Minimum of 5 years of total work experience, with at least 3 years in an audit
                                    function in the financial sector.</li>
                                <li>Graduate degree in a relevant field required.</li>
                                <li>Professional certifications such as CIA or ACCA are highly desirable.</li>
                                <li>Fluency in English is required; proficiency in Russian and Turkish is preferred.
                                </li>
                                <li>Proficient in MS Office; familiarity with data analytics and automated audit tools
                                    is a plus.</li>
                                <li></li>
                                <li>Strong analytical, communication, and conflict management skills.</li>
                            </ul>
                        </div>
                        <div class="about__image-wrap img--full">
                            <x-img path="/images/about/bg-mb.jpg" width="330" height="260" :alt="$item['title']"
                                class="about__image" />
                            <x-img path="/images/about/bg.jpg" width="768" height="260" :alt="$item['title']"
                                class="about__image" />
                        </div>
                    </div>
                    <div class="about__acc-separator"></div>
                </div>
            @endforeach
        </div>
    </div>
</section>
