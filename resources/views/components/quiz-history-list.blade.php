<div
    {{ $attributes }}
    x-data="{ history: [], loading: true }"
    x-init="
        fetch('{{ route('quizzes.history') }}', { headers: { Accept: 'application/json' } })
            .then((response) => response.json())
            .then((data) => { history = data.attempts; })
            .finally(() => { loading = false; });
    "
>
    <div x-show="loading" class="rounded-lg border border-dashed border-gray-300 p-8 text-center text-gray-500 sm:p-10">
        Memuat riwayat nilai…
    </div>

    <div
        x-show="!loading && history.length === 0"
        x-cloak
        class="rounded-lg border border-dashed border-gray-300 p-8 text-center text-gray-500 sm:p-10"
    >
        Kamu belum mengerjakan kuis apa pun. Yuk mulai belajar dan coba kuisnya!
    </div>

    <div x-show="!loading && history.length > 0" x-cloak class="overflow-x-auto rounded-xl border border-gray-200 bg-white">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-3 text-left font-medium text-gray-500 sm:px-4">Kuis</th>
                    <th class="hidden px-3 py-3 text-left font-medium text-gray-500 sm:table-cell sm:px-4">Kursus</th>
                    <th class="px-3 py-3 text-left font-medium text-gray-500 sm:px-4">Nilai</th>
                    <th class="px-3 py-3 text-left font-medium text-gray-500 sm:px-4">Status</th>
                    <th class="hidden px-3 py-3 text-left font-medium text-gray-500 md:table-cell sm:px-4">Tanggal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <template x-for="(attempt, index) in history" :key="index">
                    <tr>
                        <td class="px-3 py-3 text-gray-900 sm:px-4">
                            <span x-text="attempt.quiz_title"></span>
                            <span class="block text-xs text-gray-500 sm:hidden" x-text="attempt.course_title"></span>
                        </td>
                        <td class="hidden px-3 py-3 text-gray-600 sm:table-cell sm:px-4" x-text="attempt.course_title"></td>
                        <td class="px-3 py-3 font-medium text-gray-900 sm:px-4" x-text="`${attempt.score}/100`"></td>
                        <td class="px-3 py-3 sm:px-4">
                            <span
                                class="rounded-full px-2 py-0.5 text-xs font-medium"
                                :class="attempt.passed ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700'"
                                x-text="attempt.passed ? 'Lulus' : 'Belum Lulus'"
                            ></span>
                        </td>
                        <td
                            class="hidden px-3 py-3 text-gray-500 md:table-cell sm:px-4"
                            x-text="new Date(attempt.finished_at).toLocaleString('id-ID')"
                        ></td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</div>
