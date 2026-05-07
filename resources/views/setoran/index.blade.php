@extends('layouts.dashboard')

@section('title', 'Input Setoran Tahfidz')
@section('header', 'Input Setoran Tahfidz')

@section('content')
    <!-- Step 1: Pilih Kelas -->
    <div class="row justify-content-center mb-4" id="selectClassStep">
        <div class="col-md-10 col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-5 text-center">
                    <div class="mb-4">
                        <i class="bi bi-door-open fs-1 text-primary"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Pilih Kelas Terlebih Dahulu</h4>
                    <p class="text-muted mb-5">Silakan pilih kelas untuk memulai proses penginputan setoran hafalan siswa.
                    </p>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        @foreach ($kelas as $k)
                            <button type="button"
                                class="btn btn-outline-primary btn-lg px-5 py-3 fw-bold rounded-4 shadow-sm"
                                onclick="selectKelas('{{ $k }}')">
                                {{ $k }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 2: Input Setoran -->
    <div class="d-none" id="inputSetoranStep">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold">Form Input Setoran - <span id="displayKelas"
                                    class="text-primary"></span></h5>
                            <button class="btn btn-light btn-sm rounded-pill px-3" onclick="backToSelectClass()">
                                <i class="bi bi-arrow-left me-1"></i> Ganti Kelas
                            </button>
                        </div>
                    </div>
                    <form id="setoranForm">
                        <div class="card-body p-4">
                            <!-- Tanggal Paling Atas -->
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Tanggal Setoran</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i
                                            class="bi bi-calendar3"></i></span>
                                    <input type="text" class="form-control border-start-0" name="tanggal" id="tanggal"
                                        required readonly style="background-color: white;">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Jenis Setoran</label>
                                <select class="form-select" name="jenis_setoran" id="jenis_setoran" required>
                                    <option value="ziyadah">Ziyadah (Baru)</option>
                                    <option value="murojaah">Murojaah (Lama)</option>
                                </select>
                            </div>


                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Pilih Siswa</label>
                                <select class="form-select select2" name="siswa_id" id="siswa_id" required>
                                    <option value="">Pilih Siswa...</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Pilih Surah</label>
                                <select class="form-select select2" name="surah_id" id="surah_id" required>
                                    <option value="">Pilih Surah...</option>
                                    @foreach ($surahs as $s)
                                        <option value="{{ $s->id }}" data-ayat="{{ $s->jumlah_ayat }}">
                                            {{ $s->nomor }}. {{ $s->nama_latin }} ({{ $s->jumlah_ayat }} ayat)
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row mb-4">
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Ayat Dari</label>
                                    <input type="number" class="form-control form-control-lg" name="ayat_dari"
                                        id="ayat_dari" required min="1">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Ayat Sampai</label>
                                    <input type="number" class="form-control form-control-lg" name="ayat_sampai"
                                        id="ayat_sampai" required min="1">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Status Kelancaran</label>
                                <select class="form-select" name="status" id="status" required>
                                    <option value="lancar">Lancar</option>
                                    <option value="cukup">Cukup</option>
                                    <option value="kurang">Kurang</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-3 shadow-sm mt-3"
                                id="btnSave">
                                <i class="bi bi-cloud-arrow-up me-2"></i> SIMPAN SETORAN
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('js')
        <script>
            let currentKelas = '';

            $(function() {
                $('.select2').select2({
                    theme: 'bootstrap-5',
                    width: '100%'
                });

                // Initialize Single DatePicker
                $('#tanggal').daterangepicker({
                    singleDatePicker: true,
                    autoApply: true,
                    showDropdowns: true,
                    startDate: moment(),
                    minYear: 2020,
                    maxYear: parseInt(moment().format('YYYY'), 10),
                    locale: {
                        format: 'DD/MM/YYYY',
                        applyLabel: "Pilih",
                        cancelLabel: "Batal",
                    }
                });

                $('#setoranForm').on('submit', function(e) {
                    e.preventDefault();

                    // Konversi tanggal DD/MM/YYYY ke YYYY-MM-DD sebelum dikirim ke server
                    let formData = $(this).serializeArray();
                    let dateIndex = formData.findIndex(item => item.name === 'tanggal');
                    if (dateIndex !== -1) {
                        formData[dateIndex].value = moment(formData[dateIndex].value, 'DD/MM/YYYY').format(
                            'YYYY-MM-DD');
                    }

                    // FE Validation
                    const maxAyat = parseInt($('#surah_id').find(':selected').data('ayat'));
                    const dari = parseInt($('#ayat_dari').val());
                    const sampai = parseInt($('#ayat_sampai').val());

                    if (dari > maxAyat || sampai > maxAyat) {
                        showMessage('error', 'Cek Ayat',
                            `Ayat tidak boleh melebihi jumlah ayat surah (${maxAyat} ayat)`);
                        return;
                    }

                    if (sampai < dari) {
                        showMessage('error', 'Cek Ayat', 'Ayat sampai tidak boleh lebih kecil dari ayat dari');
                        return;
                    }

                    $('#btnSave').prop('disabled', true).html(
                        '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

                    $.ajax({
                        url: "{{ route('setoran.store') }}",
                        method: "POST",
                        data: formData,
                        success: function(res) {
                            showMessage('success', 'Berhasil!', res.message);
                            // Reset partial form
                            $('#surah_id').val(null).trigger('change');
                            $('#ayat_dari').val('');
                            $('#ayat_sampai').val('');
                        },
                        error: function(err) {
                            const msg = err.responseJSON.message || 'Terjadi kesalahan sistem';
                            showMessage('error', 'Gagal Simpan', msg);
                        },
                        complete: function() {
                            $('#btnSave').prop('disabled', false).html(
                                '<i class="bi bi-cloud-arrow-up me-2"></i> SIMPAN SETORAN');
                        }
                    });
                });

                $('#surah_id').on('change', function() {
                    const maxAyat = $(this).find(':selected').data('ayat');
                    if (maxAyat) {
                        $('#ayat_dari, #ayat_sampai').attr('max', maxAyat).attr('placeholder',
                            `Max: ${maxAyat}`);
                    } else {
                        $('#ayat_dari, #ayat_sampai').removeAttr('max').attr('placeholder', '');
                    }
                });

                // Real-time validation visual feedback
                $('#ayat_dari, #ayat_sampai').on('input', function() {
                    const max = parseInt($(this).attr('max'));
                    const val = parseInt($(this).val());
                    if (val > max) {
                        $(this).addClass('is-invalid');
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });
            });

            function selectKelas(kelas) {
                currentKelas = kelas;
                $('#displayKelas').text(kelas);
                $('#selectClassStep').addClass('d-none');
                $('#inputSetoranStep').removeClass('d-none');

                // Reset form data sebelumnya
                $('#setoranForm')[0].reset();
                $('#siswa_id, #surah_id').val(null).trigger('change');

                // Set ulang tanggal ke hari ini agar tidak kosong
                $('#tanggal').val(moment().format('DD/MM/YYYY'));

                // Load students
                $.get("{{ route('setoran.siswa') }}", {
                    kelas: kelas
                }, function(data) {
                    let options = '<option value="">Pilih Siswa...</option>';
                    data.forEach(siswa => {
                        options += `<option value="${siswa.id}">${siswa.nama}</option>`;
                    });
                    $('#siswa_id').html(options).trigger('change');
                });
            }

            function backToSelectClass() {
                $('#selectClassStep').removeClass('d-none');
                $('#inputSetoranStep').addClass('d-none');
            }
        </script>
    @endpush
@endsection
