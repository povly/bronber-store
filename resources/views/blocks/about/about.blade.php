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
            'title' => 'Зарождение компании',
            'text' => "PASHA Holding invites experienced candidates to apply for the position of Audit Manager within the Group Audit Department. The Audit Manager will play a critical role in formulating and executing the Group Audit Strategy, ensuring the integrity and effectiveness of our internal audit processes.\n\nJob description:\n\nParticipate in the formulation and execution of a three-year Group Audit Strategy, ensuring alignment across financial sector companies.\nEstablish and monitor the implementation of unified audit policies and procedures for Internal Audit Departments (IADs) within Strategic Assets.\nManage and execute the Quality Assurance and Improvement Program (QAIP) for internal audits, including both internal and external assessments.\nAssist Audit Committees in compliance activities, including preparation of charters and agendas, and regular reporting (internal and external).",
            'image' => '/images/blog/1.jpg',
        ],
        [
            'year' => '2018',
            'title' => 'Зарождение компании',
            'text' => "Review and confirm the accuracy and timeliness of audit engagement reports by IADs of Strategic Assets, and oversee annual audit plans and findings.\nFacilitate collaboration between internal and external audit functions to minimize duplication and enhance coverage.\nConduct audit engagements in Strategic Assets;\nProvide training and support to internal auditors to enhance their skills and knowledge.",
            'image' => '/images/blog/2.jpg',
        ],
        [
            'year' => '2019',
            'title' => 'Зарождение компании',
            'text' => "Experience, Competencies and Skills Required:\n\nMinimum of 5 years of total work experience, with at least 3 years in an audit function in the financial sector.\nGraduate degree in a relevant field required.\nProfessional certifications such as CIA or ACCA are highly desirable.\nFluency in English is required; proficiency in Russian and Turkish is preferred.",
            'image' => '/images/blog/3.jpg',
        ],
        [
            'year' => '2020',
            'title' => 'Зарождение компании',
            'text' => "Proficient in MS Office; familiarity with data analytics and automated audit tools is a plus.\nStrong analytical, communication, and conflict management skills.\n\nThe Audit Manager will play a critical role in formulating and executing the Group Audit Strategy, ensuring the integrity and effectiveness of our internal audit processes.",
            'image' => '/images/blog/1.jpg',
        ],
        [
            'year' => '2021',
            'title' => 'Зарождение компании',
            'text' => "Participate in the formulation and execution of a three-year Group Audit Strategy, ensuring alignment across financial sector companies.\nEstablish and monitor the implementation of unified audit policies and procedures for Internal Audit Departments (IADs) within Strategic Assets.",
            'image' => '/images/blog/2.jpg',
        ],
    ];
@endphp

<section class="about" x-data="about({{ count($timeline) }})">
    <div class="container">
        <h1 class="about__title">Наша история</h1>

        <div class="about__years">
            @foreach ($timeline as $i => $item)
                <button type="button"
                        class="about__year"
                        :class="{ 'is-active': active === {{ $i }} }"
                        :style="{ color: yearColor({{ $i }}) }"
                        @click="select({{ $i }})">
                    {{ $item['year'] }}
                </button>
            @endforeach
            <span class="about__marker" :style="{ left: markerLeft + '%' }">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 0L20 10L10 20L0 10L10 0Z" fill="currentColor"/>
                </svg>
            </span>
        </div>

        <div class="about__separator"></div>

        <div class="about__panels">
            @foreach ($timeline as $i => $item)
                <div class="about__panel" x-show="active === {{ $i }}" x-collapse>
                    <h2 class="about__subtitle">{{ $item['year'] }} -- {{ $item['title'] }}</h2>
                    <div class="about__text">{!! nl2br(e($item['text'])) !!}</div>
                    <div class="about__image-wrap">
                        <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}" class="about__image">
                    </div>
                </div>
            @endforeach
        </div>

        <div class="about__more">
            <div class="about__more-separator"></div>
            <div class="about__more-list">
                @foreach ($timeline as $i => $item)
                    <button type="button"
                            class="about__more-year"
                            :class="{ 'is-active': active === {{ $i }} }"
                            @click="select({{ $i }})">
                        {{ $item['year'] }} -- {{ $item['title'] }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>
</section>
