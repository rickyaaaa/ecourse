@props(['limit' => null])

<div
    {{ $attributes }}
    x-data="{
        history: [],
        loading: true,
        limit: {{ $limit ?? 'null' }},
        get visible() {
            return this.limit ? this.history.slice(0, this.limit) : this.history;
        },
    }"
    x-init="
        fetch('{{ route('quizzes.history') }}', { headers: { Accept: 'application/json' } })
            .then((response) => response.json())
            .then((data) => { history = data.attempts; })
            .finally(() => { loading = false; });
    "
>
    <div x-show="loading" class="text-center text-body p-4 border border-dashed rounded-4">
        Memuat riwayat nilai…
    </div>

    <div x-show="!loading && history.length === 0" x-cloak class="text-center text-body p-4 border border-dashed rounded-4">
        Kamu belum mengerjakan kuis apa pun. Yuk mulai belajar dan coba kuisnya!
    </div>

    <div x-show="!loading && history.length > 0" x-cloak class="table-responsive">
        <table class="table align-middle bg-white rounded-4 overflow-hidden mb-0">
            <thead class="table-light">
                <tr>
                    <th>Kuis</th>
                    <th class="d-none d-sm-table-cell">Kursus</th>
                    <th>Nilai</th>
                    <th>Status</th>
                    <th class="d-none d-md-table-cell">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="(attempt, index) in visible" :key="index">
                    <tr>
                        <td>
                            <span x-text="attempt.quiz_title"></span>
                            <span class="d-block small text-body d-sm-none" x-text="attempt.course_title"></span>
                        </td>
                        <td class="d-none d-sm-table-cell text-body" x-text="attempt.course_title"></td>
                        <td class="fw-semibold" x-text="`${attempt.score}/100`"></td>
                        <td>
                            <span
                                class="badge rounded-pill"
                                :class="attempt.passed ? 'text-bg-success' : 'text-bg-warning'"
                                x-text="attempt.passed ? 'Lulus' : 'Belum Lulus'"
                            ></span>
                        </td>
                        <td class="d-none d-md-table-cell text-body" x-text="new Date(attempt.finished_at).toLocaleString('id-ID')"></td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</div>
