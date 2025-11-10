@extends('layouts.app')

@section('extra_css')
@endsection

{{-- @section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb my-0">
            <li class="breadcrumb-item"><span>Ceisa H2H</span></li>
            <li class="breadcrumb-item"><span>Dokumen Pabean</span></li>
            <li class="breadcrumb-item active"><span>BC 2.0</span></li>
        </ol>
    </nav>
@endsection --}}

@section('content')
    <div class="container-lg px-3">
        {{-- <div class="row mb-4">
            <div class="col-sm-6 col-xl-3" data-coreui-toggle="modal" data-coreui-target="#modalNewDocument">
                <div class="card text-white bg-info">
                    <div class="card-body p-2 d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fs-7 fw-semibold"><i class="fa-solid fa-file"></i> New Document</div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

        <div class="card mb-4">
            <div class="card-header">
                <div class="card-title">BC 2.0 - Pemberitahuan Impor Barang</div>
            </div>
            <div class="card-body">
                <ul class="nav nav-pills mb-2" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pills-header-tab" data-coreui-toggle="pill"
                            data-coreui-target="#pills-header" type="button" role="tab" aria-controls="pills-header"
                            aria-selected="true">Header</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-entitas-tab" data-coreui-toggle="pill"
                            data-coreui-target="#pills-entitas" type="button" role="tab" aria-controls="pills-entitas"
                            aria-selected="false">Entitas</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-dokumen-tab" data-coreui-toggle="pill"
                            data-coreui-target="#pills-dokumen" type="button" role="tab" aria-controls="pills-dokumen"
                            aria-selected="false">Dokumen</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-pengangkut-tab" data-coreui-toggle="pill"
                            data-coreui-target="#pills-pengangkut" type="button" role="tab" aria-controls="pills-pengangkut"
                            aria-selected="true">Pengangkut</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-kemasan-tab" data-coreui-toggle="pill"
                            data-coreui-target="#pills-kemasan" type="button" role="tab" aria-controls="pills-kemasan"
                            aria-selected="false">Kemasan & Peti Kemas</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-transaksi-tab" data-coreui-toggle="pill"
                            data-coreui-target="#pills-transaksi" type="button" role="tab" aria-controls="pills-transaksi"
                            aria-selected="false">Transaksi</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-barang-tab" data-coreui-toggle="pill"
                            data-coreui-target="#pills-barang" type="button" role="tab" aria-controls="pills-barang"
                            aria-selected="true">Barang</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-pungutan-tab" data-coreui-toggle="pill"
                            data-coreui-target="#pills-pungutan" type="button" role="tab" aria-controls="pills-pungutan"
                            aria-selected="false">Pungutan</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-persyaratan-tab" data-coreui-toggle="pill"
                            data-coreui-target="#pills-persyaratan" type="button" role="tab" aria-controls="pills-persyaratan"
                            aria-selected="false">Pernyataan</button>
                    </li>
                </ul>
                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="pills-header" role="tabpanel" aria-labelledby="pills-header-tab" tabindex="0">
                        <div class="row">
                            <div class="col-sm-4 mb-3 mb-sm-0">                          
                                <div class="card mb-4">
                                    <div class="card-header" style="border-bottom: var(--cui-card-border-width) solid var(--cui-card-border-color); background-color: var(--cui-gray-200)">
                                        <h6 class="card-title text-body-secondary">Pengajuan</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="nomorAju" class="form-label">Nomor Pengajuan</label>
                                            <input type="text" class="form-control" name="nomorAju" id="nomorAju" spellcheck="false" autocomplete="off" placeholder="000020-017710-20240710-211009">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4 mb-3 mb-sm-0">                          
                                <div class="card mb-4">
                                    <div class="card-header" style="border-bottom: var(--cui-card-border-width) solid var(--cui-card-border-color); background-color: var(--cui-gray-200)">
                                        <h6 class="card-title text-body-secondary">Kantor Pabean</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="kodePelTujuan" class="form-label">Pelabuhan Tujuan</label>
                                            <select class="select2 is-invalid" name="kodePelTujuan" id="kodePelTujuan" aria-describedby="kodePelTujuanFeedback">
                                                <option></option>
                                            </select>
                                            <div id="kodePelTujuanFeedback" class="invalid-feedback">Pelabuhan Tujuan Kosong</div>                                          
                                        </div>
                                        <div class="mb-3">
                                            <label for="kodeKantor" class="form-label">Kantor Pabean</label>
                                            <select class="select2" name="kodeKantor" id="kodeKantor" placeholder="" disabled>
                                                <option></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4 mb-3 mb-sm-0">                          
                                <div class="card mb-4">
                                    <div class="card-header" style="border-bottom: var(--cui-card-border-width) solid var(--cui-card-border-color); background-color: var(--cui-gray-200)">
                                        <h6 class="card-title text-body-secondary">Keterangan Lain</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="kodeJenisProsedur" class="form-label">Jenis PIB</label>
                                            <select class="select2" name="kodeJenisProsedur" id="kodeJenisProsedur" aria-describedby="kodeJenisProsedurFeedback">
                                                <option></option>
                                                <option value="1" title="1 - BIASA">1 - BIASA</option>
                                                <option value="2" title="2 - BERKALA">2 - BERKALA</option>
                                            </select>
                                            <div id="kodeJenisProsedurFeedback" class="invalid-feedback">Jenis PIB Kosong</div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="kodeJenisImpor" class="form-label">Jenis Impor</label>
                                            <select class="select2" name="kodeJenisImpor" id="kodeJenisImpor" aria-describedby="kodeJenisImporFeedback">
                                                <option></option>
                                                @foreach ($getJenisImpor as $rowJenisImpor)
                                                    <option value="{{ $rowJenisImpor->kode_jenis_impor }}" title="{{ $rowJenisImpor->kode_jenis_impor }} - {{ $rowJenisImpor->nama_jenis_impor }}">{{ $rowJenisImpor->kode_jenis_impor }} - {{ $rowJenisImpor->nama_jenis_impor }}</option>
                                                @endforeach
                                            </select>
                                            <div id="kodeJenisImporFeedback" class="invalid-feedback">Jenis Impor Kosong</div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="kodeCaraBayar" class="form-label">Cara Bayar</label>
                                            <select class="select2" name="kodeCaraBayar" id="kodeCaraBayar" aria-describedby="kodeCaraBayarFeedback">
                                                <option></option>
                                                @foreach ($getCaraBayar as $rowCaraBayar)
                                                    <option value="{{ $rowCaraBayar->kode_cara_bayar }}" title="{{ $rowCaraBayar->kode_cara_bayar }} - {{ $rowCaraBayar->nama_cara_bayar }}">{{ $rowCaraBayar->kode_cara_bayar }} - {{ $rowCaraBayar->nama_cara_bayar }}</option>
                                                @endforeach
                                            </select>
                                            <div id="kodeCaraBayarFeedback" class="invalid-feedback">Cara Bayar Kosong</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="pills-entitas" role="tabpanel" aria-labelledby="pills-entitas-tab" tabindex="1">
                        <div class="row">
                            <div class="col-sm-4 mb-3 mb-sm-0">                          
                                <div class="card mb-4">
                                    <div class="card-header" style="border-bottom: var(--cui-card-border-width) solid var(--cui-card-border-color); background-color: var(--cui-gray-200)">
                                        <h6 class="card-title text-body-secondary" style="display: inline">Importir</h6>
                                        <div style="display: inline; float: right">
                                            <button type="button" class="btn btn-info referensi toggleAside" data-asidetitle="Referensi Pengusaha" data-type="Entitas" data-src="Importir" style="--cui-btn-padding-y: .25rem; --cui-btn-padding-x: .5rem; --cui-btn-font-size: .8rem;"><i class="fa-regular fa-building-magnifying-glass"></i> Referensi</button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <input type="hidden" id="kodeEntitasImportir" name="kodeEntitas[]" value="{{ $jenisEntitas['IMPORTIR'] }}">
                                        <div class="mb-3">
                                            <label for="kodeJenisIdentitasImportir" class="form-label">Nomor Identitas</label>
                                            <div class="input-group mb-3">
                                                <div class="col-sm-4">
                                                    <select class="select2 kodeJenisIdentitas" name="kodeJenisIdentitas[]" id="kodeJenisIdentitasImportir" aria-describedby="kodeJenisIdentitasImportirFeedback">
                                                        <option></option>
                                                        @foreach ($getJenisIdentitas as $rowJenisIdentitas)
                                                            <option value="{{ $rowJenisIdentitas->kode_jenis_identitas }}" title="{{ $rowJenisIdentitas->kode_jenis_identitas }} - {{ $rowJenisIdentitas->nama_jenis_identitas }}">{{ $rowJenisIdentitas->kode_jenis_identitas }} - {{ $rowJenisIdentitas->nama_jenis_identitas }}</option>
                                                        @endforeach
                                                    </select>
                                                    <div id="kodeJenisIdentitasImportirFeedback" class="invalid-feedback">Jenis Entitas Kosong</div>
                                                </div>
                                                <div class="col-sm-8" style="padding-left: 5px;">
                                                    <input type="text" class="form-control border-top-left-radius border-bottom-left-radius nomorIdentitas" name="nomorIdentitas[]" id="nomorIdentitasImportir" spellcheck="false" autocomplete="off" placeholder="" aria-describedby="nomorIdentitasImportirFeedback">
                                                    <div id="nomorIdentitasImportirFeedback" class="invalid-feedback">Nomor Entitas Kosong</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="namaEntitasImportir" class="form-label">Nama</label>
                                            <input type="text" class="form-control namaEntitas" id="namaEntitasImportir" name="namaEntitas[]" spellcheck="false" autocomplete="off" placeholder="" aria-describedby="namaEntitasImportirFeedback">
                                            <div id="namaEntitasImportirFeedback" class="invalid-feedback">Nama Kosong</div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="alamatEntitasImportir" class="form-label">Alamat</label>
                                            <textarea class="form-control autosize alamatEntitas" spellcheck="false" placeholder="" id="alamatEntitasImportir" name="alamatEntitas[]" aria-describedby="alamatEntitasImportirFeedback"></textarea>
                                            <div id="alamatEntitasImportirFeedback" class="invalid-feedback">Alamat Kosong</div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="kodeJenisApiImportir" class="form-label">API / NIB</label>
                                            <div class="input-group mb-3">
                                                <div class="col-sm-4">
                                                    <select class="select2 kodeJenisApi" name="kodeJenisApi[]" id="kodeJenisApiImportir">
                                                        <option></option>
                                                        @foreach ($getJenisApiEntitas as $rowData)
                                                            <option value="{{ $rowData->kode_jenis_api_entitas }}" title="{{ $rowData->kode_jenis_api_entitas }} - {{ $rowData->nama_jenis_api_entitas }}">{{ $rowData->kode_jenis_api_entitas }} - {{ $rowData->nama_jenis_api_entitas }}</option>
                                                        @endforeach
                                                    </select>
                                                    <div id="kodeJenisApiImportirFeedback" class="invalid-feedback">Jenis Api Kosong</div>
                                                </div>
                                                <div class="col-sm-8" style="padding-left: 5px;">
                                                    <input type="text" class="form-control border-top-left-radius border-bottom-left-radius nibEntitas" name="nibEntitas[]" id="nibEntitasImportir" spellcheck="false" autocomplete="off" placeholder="" aria-describedby="nibEntitasImportirFeedback">
                                                    <div id="nibEntitasImportirFeedback" class="invalid-feedback">NIB Kosong</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="statusEntitasImportir" class="form-label">Status</label>
                                            <select class="select2" name="statusEntitas[]" id="statusEntitasImportir" aria-describedby="statusEntitasImportirFeedback">
                                                <option></option>
                                            </select>
                                            <div id="statusEntitasImportirFeedback" class="invalid-feedback">Status Kosong</div>
                                        </div>

                                        <input type="hidden" id="namaNegaraImportir" name="namaNegara[]" value="">

                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4 mb-3 mb-sm-0">                          
                                <div class="card mb-4">
                                    <div class="card-header" style="border-bottom: var(--cui-card-border-width) solid var(--cui-card-border-color); background-color: var(--cui-gray-200">
                                        <h6 class="card-title text-body-secondary" style="display: inline">Pemilik Barang</h6>
                                        <div style="display: inline; float: right">
                                            <button type="button" class="btn btn-info salinEntitas" data-src="Importir" data-dest="Pemilik" style="--cui-btn-padding-y: .25rem; --cui-btn-padding-x: .5rem; --cui-btn-font-size: .8rem;"><i class="fa-regular fa-copy"></i> Salin Importir</button>
                                            <button type="button" class="btn btn-info referensi toggleAside" data-asidetitle="Referensi Pemilik" data-type="Entitas" data-src="Pemilik" style="--cui-btn-padding-y: .25rem; --cui-btn-padding-x: .5rem; --cui-btn-font-size: .8rem;"><i class="fa-regular fa-building-magnifying-glass"></i> Referensi</button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <input type="hidden" id="kodeEntitasPemilik" name="kodeEntitas[]" value="{{ $jenisEntitas['PEMILIK'] }}">
                                        <div class="mb-3">
                                            <label for="kodeJenisIdentitasPemilik" class="form-label">Nomor Identitas</label>
                                            <div class="input-group mb-3">
                                                <div class="col-sm-4">
                                                    <select class="select2 kodeJenisIdentitas" name="kodeJenisIdentitas[]" id="kodeJenisIdentitasPemilik" aria-describedby="kodeJenisIdentitasPemilikFeedback">
                                                        <option></option>
                                                        @foreach ($getJenisIdentitas as $rowJenisIdentitas)
                                                            <option value="{{ $rowJenisIdentitas->kode_jenis_identitas }}" title="{{ $rowJenisIdentitas->kode_jenis_identitas }} - {{ $rowJenisIdentitas->nama_jenis_identitas }}">{{ $rowJenisIdentitas->kode_jenis_identitas }} - {{ $rowJenisIdentitas->nama_jenis_identitas }}</option>
                                                        @endforeach
                                                    </select>
                                                    <div id="kodeJenisIdentitasPemilikFeedback" class="invalid-feedback">Jenis Entitas Kosong</div>
                                                </div>
                                                <div class="col-sm-8" style="padding-left: 5px;">
                                                    <input type="text" class="form-control border-top-left-radius border-bottom-left-radius nomorIdentitas" name="nomorIdentitas[]" id="nomorIdentitasPemilik" spellcheck="false" autocomplete="off" placeholder="" aria-describedby="nomorIdentitasPemilikFeedback">
                                                    <div id="nomorIdentitasPemilikFeedback" class="invalid-feedback">Nomor Entitas Kosong</div>
                                                </div>
                                            </div>
                                        </div>
                            
                                        <div class="mb-3">
                                            <label for="namaEntitasPemilik" class="form-label">Nama</label>
                                            <input type="text" class="form-control namaEntitas" id="namaEntitasPemilik" name="namaEntitas[]" spellcheck="false" autocomplete="off" placeholder="" aria-describedby="namaEntitasPemilikFeedback">
                                            <div id="namaEntitasPemilikFeedback" class="invalid-feedback">Nama Kosong</div>
                                        </div>
                            
                                        <div class="mb-3">
                                            <label for="alamatEntitasPemilik" class="form-label">Alamat</label>
                                            <textarea class="form-control autosize alamatEntitas" spellcheck="false" placeholder="" id="alamatEntitasPemilik" name="alamatEntitas[]" aria-describedby="alamatEntitasPemilikFeedback"></textarea>
                                            <div id="alamatEntitasPemilikFeedback" class="invalid-feedback">Alamat Kosong</div>
                                        </div>
                            
                                        <input type="hidden" id="namaNegaraPemilik" name="namaNegara[]" value="">
                                        <input type="hidden" id="kodeJenisApiPemilik" name="kodeJenisApi[]" value="">
                                        <input type="hidden" id="nibEntitasPemilik" name="nibEntitas[]" value="">
                                        <input type="hidden" id="statusEntitasPemilik" name="statusEntitas[]" value="">
                            
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4 mb-3 mb-sm-0">                          
                                <div class="card mb-4">
                                    <div class="card-header" style="border-bottom: var(--cui-card-border-width) solid var(--cui-card-border-color); background-color: var(--cui-gray-200)">
                                        <h6 class="card-title text-body-secondary" style="display: inline">NPWP Pemusatan</h6>
                                        <div style="display: inline; float: right">
                                            <button type="button" class="btn btn-info salinEntitas" data-src="Importir" data-dest="Pemusatan" style="--cui-btn-padding-y: .25rem; --cui-btn-padding-x: .5rem; --cui-btn-font-size: .8rem;"><i class="fa-regular fa-copy"></i> Salin Importir</button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <input type="hidden" id="kodeEntitasPemusatan" name="kodeEntitas[]" value="{{ $jenisEntitas['PEMUSATAN'] }}">
                                        <div class="mb-3">
                                            <label for="kodeJenisIdentitasPemusatan" class="form-label">Nomor Identitas</label>
                                            <div class="input-group mb-3">
                                                <div class="col-sm-4">
                                                    <select class="select2 kodeJenisIdentitas" name="kodeJenisIdentitas[]" id="kodeJenisIdentitasPemusatan" aria-describedby="kodeJenisIdentitasPemusatanFeedback">
                                                        <option></option>
                                                        @foreach ($getJenisIdentitas as $rowJenisIdentitas)
                                                            <option value="{{ $rowJenisIdentitas->kode_jenis_identitas }}" title="{{ $rowJenisIdentitas->kode_jenis_identitas }} - {{ $rowJenisIdentitas->nama_jenis_identitas }}">{{ $rowJenisIdentitas->kode_jenis_identitas }} - {{ $rowJenisIdentitas->nama_jenis_identitas }}</option>
                                                        @endforeach
                                                    </select>
                                                    <div id="kodeJenisIdentitasPemusatanFeedback" class="invalid-feedback">Jenis Entitas Kosong</div>
                                                </div>
                                                <div class="col-sm-8" style="padding-left: 5px;">
                                                    <input type="text" class="form-control border-top-left-radius border-bottom-left-radius nomorIdentitas" name="nomorIdentitas[]" id="nomorIdentitasPemusatan" spellcheck="false" autocomplete="off" placeholder="" aria-describedby="nomorIdentitasPemusatanFeedback">
                                                    <div id="nomorIdentitasPemusatanFeedback" class="invalid-feedback">Nomor Entitas Kosong</div>
                                                </div>
                                            </div>
                                        </div>
                            
                                        <div class="mb-3">
                                            <label for="namaEntitasPemusatan" class="form-label">Nama</label>
                                            <input type="text" class="form-control namaEntitas" id="namaEntitasPemusatan" name="namaEntitas[]" spellcheck="false" autocomplete="off" placeholder="" aria-describedby="namaEntitasPemusatanFeedback">
                                            <div id="namaEntitasPemusatanFeedback" class="invalid-feedback">Nama Kosong</div>
                                        </div>
                            
                                        <div class="mb-3">
                                            <label for="alamatEntitasPemusatan" class="form-label">Alamat</label>
                                            <textarea class="form-control autosize alamatEntitas" spellcheck="false" placeholder="" id="alamatEntitasPemusatan" name="alamatEntitas[]" aria-describedby="alamatEntitasPemusatanFeedback"></textarea>
                                            <div id="alamatEntitasPemusatanFeedback" class="invalid-feedback">Alamat Kosong</div>
                                        </div>
                            
                                        <input type="hidden" id="namaNegaraPemusatan" name="namaNegara[]" value="">
                                        <input type="hidden" id="kodeJenisApiPemusatan" name="kodeJenisApi[]" value="">
                                        <input type="hidden" id="nibEntitasPemusatan" name="nibEntitas[]" value="">
                                        <input type="hidden" id="statusEntitasPemusatan" name="statusEntitas[]" value="">
                            
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4 mb-3 mb-sm-0">                          
                                <div class="card mb-4">
                                    <div class="card-header" style="border-bottom: var(--cui-card-border-width) solid var(--cui-card-border-color); background-color: var(--cui-gray-200)">
                                        <h6 class="card-title text-body-secondary" style="display: inline">Pengirim</h6>
                                        <div style="display: inline; float: right">
                                            <button type="button" class="btn btn-info referensi toggleAside" data-asidetitle="Referensi Pengirim" data-type="Entitas" data-src="Pengirim" style="--cui-btn-padding-y: .25rem; --cui-btn-padding-x: .5rem; --cui-btn-font-size: .8rem;"><i class="fa-regular fa-building-magnifying-glass"></i> Referensi</button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <input type="hidden" id="kodeEntitasPengirim" name="kodeEntitas[]" value="{{ $jenisEntitas['PENGIRIM'] }}">
                                        <input type="hidden" name="kodeJenisIdentitas[]" value="">
                                        <input type="hidden" name="nomorIdentitas[]" value="">
                            
                                        <div class="mb-3">
                                            <label for="namaEntitasPengirim" class="form-label">Nama</label>
                                            <input type="text" class="form-control namaEntitas" id="namaEntitasPengirim" name="namaEntitas[]" spellcheck="false" autocomplete="off" placeholder="" aria-describedby="namaEntitasPengirimFeedback">
                                            <div id="namaEntitasPengirimFeedback" class="invalid-feedback">Nama Kosong</div>
                                        </div>
                            
                                        <div class="mb-3">
                                            <label for="alamatEntitasPengirim" class="form-label">Alamat</label>
                                            <textarea class="form-control autosize alamatEntitas" spellcheck="false" placeholder="" id="alamatEntitasPengirim" name="alamatEntitas[]" aria-describedby="alamatEntitasPengirimFeedback"></textarea>
                                            <div id="alamatEntitasPengirimFeedback" class="invalid-feedback">Alamat Kosong</div>
                                        </div>
                            
                                        <div class="mb-3">
                                            <label for="namaNegaraPengirim" class="form-label">Negara</label>
                                            <select class="select2 selectNegara" name="namaNegara[]" id="namaNegaraPengirim" aria-describedby="namaNegaraPengirimFeedback">
                                                <option></option>
                                            </select>
                                            <div id="namaNegaraPengirimFeedback" class="invalid-feedback">Negara Kosong</div>
                                        </div>
                            
                                        <input type="hidden" id="kodeJenisApiPengirim" name="kodeJenisApi[]" value="">
                                        <input type="hidden" id="nibEntitasPengirim" name="nibEntitas[]" value="">
                                        <input type="hidden" id="statusEntitasPengirim" name="statusEntitas[]" value="">
                            
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-4 mb-3 mb-sm-0">                          
                                <div class="card mb-4">
                                    <div class="card-header" style="border-bottom: var(--cui-card-border-width) solid var(--cui-card-border-color); background-color: var(--cui-gray-200)">
                                        <h6 class="card-title text-body-secondary" style="display: inline">Penjual</h6>
                                        <div style="display: inline; float: right">
                                            <button type="button" class="btn btn-info salinEntitas" data-src="Pengirim" data-dest="Penjual" style="--cui-btn-padding-y: .25rem; --cui-btn-padding-x: .5rem; --cui-btn-font-size: .8rem;"><i class="fa-regular fa-copy"></i> Salin Pengirim</button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <input type="hidden" id="kodeEntitasPenjual" name="kodeEntitas[]" value="{{ $jenisEntitas['PENJUAL'] }}">
                                        <input type="hidden" name="kodeJenisIdentitas[]" value="">
                                        <input type="hidden" name="nomorIdentitas[]" value="">
                            
                                        <div class="mb-3">
                                            <label for="namaEntitasPenjual" class="form-label">Nama</label>
                                            <input type="text" class="form-control namaEntitas" id="namaEntitasPenjual" name="namaEntitas[]" spellcheck="false" autocomplete="off" placeholder="" aria-describedby="namaEntitasPenjualFeedback">
                                            <div id="namaEntitasPenjualFeedback" class="invalid-feedback">Nama Kosong</div>
                                        </div>
                            
                                        <div class="mb-3">
                                            <label for="alamatEntitasPenjual" class="form-label">Alamat</label>
                                            <textarea class="form-control autosize alamatEntitas" spellcheck="false" placeholder="" id="alamatEntitasPenjual" name="alamatEntitas[]" aria-describedby="alamatEntitasPenjualFeedback"></textarea>
                                            <div id="alamatEntitasPenjualFeedback" class="invalid-feedback">Alamat Kosong</div>
                                        </div>
                            
                                        <div class="mb-3">
                                            <label for="namaNegaraPenjual" class="form-label">Negara</label>
                                            <select class="select2 selectNegara" name="namaNegara[]" id="namaNegaraPenjual" aria-describedby="namaNegaraPenjualFeedback">
                                                <option></option>
                                            </select>
                                            <div id="namaNegaraPenjualFeedback" class="invalid-feedback">Negara Kosong</div>
                                        </div>
                            
                                        <input type="hidden" id="kodeJenisApiPenjual" name="kodeJenisApi[]" value="">
                                        <input type="hidden" id="nibEntitasPenjual" name="nibEntitas[]" value="">
                                        <input type="hidden" id="statusEntitasPenjual" name="statusEntitas[]" value="">
                            
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-4 mb-3 mb-sm-0">                          
                                <div class="card mb-4">
                                    <div class="card-header" style="border-bottom: var(--cui-card-border-width) solid var(--cui-card-border-color); background-color: var(--cui-gray-200)">
                                        <h6 class="card-title text-body-secondary">PPJK</h6>
                                    </div>
                                    <div class="card-body">
                                        <input type="hidden" id="kodeEntitasPpjk" name="kodeEntitas[]" value="{{ $jenisEntitas['PPJK'] }}">
                                        <div class="mb-3">
                                            <label for="kodeJenisIdentitasPpjk" class="form-label">Nomor Identitas</label>
                                            <div class="input-group mb-3">
                                                <div class="col-sm-4">
                                                    <select class="select2 kodeJenisIdentitas" name="kodeJenisIdentitas[]" id="kodeJenisIdentitasPpjk" aria-describedby="kodeJenisIdentitasPpjkFeedback">
                                                        <option></option>
                                                        @foreach ($getJenisIdentitas as $rowJenisIdentitas)
                                                            <option value="{{ $rowJenisIdentitas->kode_jenis_identitas }}" title="{{ $rowJenisIdentitas->kode_jenis_identitas }} - {{ $rowJenisIdentitas->nama_jenis_identitas }}">{{ $rowJenisIdentitas->kode_jenis_identitas }} - {{ $rowJenisIdentitas->nama_jenis_identitas }}</option>
                                                        @endforeach
                                                    </select>
                                                    <div id="kodeJenisIdentitasPpjkFeedback" class="invalid-feedback">Jenis Entitas Kosong</div>
                                                </div>
                                                <div class="col-sm-8" style="padding-left: 5px;">
                                                    <input type="text" class="form-control border-top-left-radius border-bottom-left-radius nomorIdentitas" name="nomorIdentitas[]" id="nomorIdentitasPpjk" spellcheck="false" autocomplete="off" placeholder="" aria-describedby="nomorIdentitasPpjkFeedback">
                                                    <div id="nomorIdentitasPpjkFeedback" class="invalid-feedback">Nomor Entitas Kosong</div>
                                                </div>
                                            </div>
                                        </div>
                            
                                        <div class="mb-3">
                                            <label for="namaEntitasPpjk" class="form-label">Nama</label>
                                            <input type="text" class="form-control namaEntitas" id="namaEntitasPpjk" name="namaEntitas[]" spellcheck="false" autocomplete="off" placeholder="" aria-describedby="namaEntitasPpjkFeedback">
                                            <div id="namaEntitasPpjkFeedback" class="invalid-feedback">Nama Kosong</div>
                                        </div>
                            
                                        <div class="mb-3">
                                            <label for="alamatEntitasPpjk" class="form-label">Alamat</label>
                                            <textarea class="form-control autosize alamatEntitas" spellcheck="false" placeholder="" id="alamatEntitasPpjk" name="alamatEntitas[]" aria-describedby="alamatEntitasPpjkFeedback"></textarea>
                                            <div id="alamatEntitasPpjkFeedback" class="invalid-feedback">Alamat Kosong</div>
                                        </div>
                            
                                        <input type="hidden" id="namaNegaraPpjk" name="namaNegara[]" value="">
                                        <input type="hidden" id="kodeJenisApiPpjk" name="kodeJenisApi[]" value="">
                                        <input type="hidden" id="nibEntitasPpjk" name="nibEntitas[]" value="">
                                        <input type="hidden" id="statusEntitasPpjk" name="statusEntitas[]" value="">
                            
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="pills-dokumen" role="tabpanel" aria-labelledby="pills-dokumen-tab" tabindex="2">
                        <div class="row">
                            <div class="col-sm-12 mb-3 mb-sm-0">                          
                                <div class="card mb-4">
                                    <div class="card-header" style="border-bottom: var(--cui-card-border-width) solid var(--cui-card-border-color); background-color: var(--cui-gray-200)">
                                        <h6 class="card-title text-body-secondary">Dokumen Lampiran</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <div class="table-responsive">
                                                <table id="example" class="table table-striped" style="width:100%">
                                                    <thead class="table-dark">
                                                        <tr>
                                                            <th>Seri</th>
                                                            <th>Jenis</th>
                                                            <th>Nomor</th>
                                                            <th>Tanggal</th>
                                                            <th>Fasilitas</th>
                                                            <th>Izin</th>
                                                            <th>Kantor</th>
                                                            <th>File</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>Tiger Nixon</td>
                                                            <td>System Architect</td>
                                                            <td>Edinburgh</td>
                                                            <td>2011-04-25</td>
                                                            <td>$320,800</td>
                                                            <td>61</td>
                                                            <td>2011-04-25</td>
                                                            <td>$320,800</td>
                                                            <td>
                                                                <div class="btn-group">
                                                                    <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-coreui-toggle="dropdown" aria-expanded="false">
                                                                    Action
                                                                    </button>
                                                                    <ul class="dropdown-menu">
                                                                    <li><a class="dropdown-item" href="#">Action</a></li>
                                                                    <li><a class="dropdown-item" href="#">Another action</a></li>
                                                                    <li><a class="dropdown-item" href="#">Something else here</a></li>
                                                                    <li><hr class="dropdown-divider"></li>
                                                                    <li><a class="dropdown-item" href="#">Separated link</a></li>
                                                                    </ul>
                                                                </div>                                  
                                                            </td>
                                                        </tr>
                                                        
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="pills-pengangkut" role="tabpanel" aria-labelledby="pills-pengangkut-tab" tabindex="3">
                        <div class="row">
                            <div class="col-sm-4 mb-3 mb-sm-0">                          
                                <div class="card mb-4">
                                    <div class="card-header" style="border-bottom: var(--cui-card-border-width) solid var(--cui-card-border-color); background-color: var(--cui-gray-200)">
                                        <h6 class="card-title text-body-secondary">BC 1.1</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="kodeTutupPu" class="form-label">Nomor Tutup PU</label>
                                            <div class="input-group mb-3">
                                                <div class="col-sm-4">
                                                    <select class="select2" name="kodeTutupPu" id="kodeTutupPu" aria-describedby="kodeTutupPuFeedback">
                                                        <option></option>
                                                    </select>
                                                    <div id="kodeTutupPuFeedback" class="invalid-feedback">Kode Tutup Kosong</div>
                                                </div>
                                                <div class="col-sm-4" style="padding-left: 5px;">
                                                    <input type="text" class="form-control border-top-left-radius border-bottom-left-radius" name="nomorBc11" id="nomorBc11" spellcheck="false" autocomplete="off" placeholder="" aria-describedby="nomorBc11Feedback">
                                                    <div id="nomorBc11Feedback" class="invalid-feedback">Nomor Kosong</div>
                                                </div>
                                                <div class="col-sm-4" style="padding-left: 5px;">   
                                                    <div id="tanggalBc11"></div>
                                                    <div id="tanggalBc11Feedback" class="invalid-feedback">Tanggal Kosong</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="posBc11" class="form-label">Nomor Pos</label>
                                            <div class="input-group mb-3">
                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control border-top-left-radius border-bottom-left-radius" name="posBc11" id="posBc11" spellcheck="false" autocomplete="off" placeholder="" aria-describedby="posBc11Feedback">
                                                    <div id="posBc11Feedback" class="invalid-feedback">Nomor Pos Kosong</div>
                                                </div>
                                                <div class="col-sm-4" style="padding-left: 5px;">
                                                    <input type="text" class="form-control border-top-left-radius border-bottom-left-radius" name="subPosBc11" id="subPosBc11" spellcheck="false" autocomplete="off" placeholder="" aria-describedby="subPosBc11Feedback">
                                                    <div id="subPosBc11Feedback" class="invalid-feedback">Nomor Subpos Kosong</div>
                                                </div>
                                                <div class="col-sm-4" style="padding-left: 5px;">   
                                                    <input type="text" class="form-control border-top-left-radius border-bottom-left-radius" name="subSubPosBc11" id="subSubPosBc11" spellcheck="false" autocomplete="off" placeholder="" aria-describedby="subSubPosBc11Feedback">
                                                    <div id="subSubPosBc11Feedback" class="invalid-feedback">Nomor Subsubpos Kosong</div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4 mb-3 mb-sm-0">                          
                                <div class="card mb-4">
                                    <div class="card-header" style="border-bottom: var(--cui-card-border-width) solid var(--cui-card-border-color); background-color: var(--cui-gray-200)">
                                        <h6 class="card-title text-body-secondary">Pengangkutan</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="kodeCaraAngkut" class="form-label">Cara Pengangkutan</label>
                                            <select class="select2" name="kodeCaraAngkut" id="kodeCaraAngkut" aria-describedby="kodeCaraAngkutFeedback">
                                                <option></option>
                                            </select>
                                            <div id="kodeCaraAngkutFeedback" class="invalid-feedback">Cara Pengangkutan Kosong</div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="namaPengangkut" class="form-label">Nama Sarana Angkut</label>
                                            <input type="text" class="form-control" name="namaPengangkut" id="namaPengangkut" spellcheck="false" autocomplete="off" placeholder="" aria-describedby="namaPengangkutFeedback">
                                            <div id="namaPengangkutFeedback" class="invalid-feedback">Nama Sarana Angkut Kosong</div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="nomorPengangkut" class="form-label">Nomor Voy/Flight/No.Pol</label>
                                            <input type="text" class="form-control" name="nomorPengangkut" id="nomorPengangkut" spellcheck="false" autocomplete="off" placeholder="" aria-describedby="nomorPengangkutFeedback">
                                            <div id="nomorPengangkutFeedback" class="invalid-feedback">Nomor Voy/Flight/No.Pol Kosong</div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="kodeBendera" class="form-label">Bendera</label>
                                            <select class="select2" name="kodeBendera" id="kodeBendera" aria-describedby="kodeBenderaFeedback">
                                                <option></option>
                                            </select>
                                            <div id="kodeBenderaFeedback" class="invalid-feedback">Bendera Kosong</div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="tanggalTiba" class="form-label">Perkiraan Tanggal Tiba</label>
                                            <div id="tanggalTiba" aria-describedby="tanggalTibaFeedback"></div>
                                            <div id="tanggalTibaFeedback" class="invalid-feedback">Perkiraan Tanggal Tiba Kosong</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4 mb-3 mb-sm-0">                          
                                <div class="card mb-4">
                                    <div class="card-header" style="border-bottom: var(--cui-card-border-width) solid var(--cui-card-border-color); background-color: var(--cui-gray-200)">
                                        <h6 class="card-title text-body-secondary">Pelabuhan & Tempat Penimbunan</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="kodePelMuat" class="form-label">Pelabuhan Muat</label>
                                            <select class="select2" name="kodePelMuat" id="kodePelMuat" aria-describedby="kodePelMuatFeedback">
                                                <option></option>
                                            </select>
                                            <div id="kodePelMuatFeedback" class="invalid-feedback">Pelabuhan Muat Kosong</div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="kodePelTransit" class="form-label">Pelabuhan Transit</label>
                                            <select class="select2" name="kodePelTransit" id="kodePelTransit" aria-describedby="kodePelTransitFeedback">
                                                <option></option>
                                            </select>
                                            <div id="kodePelTransitFeedback" class="invalid-feedback">Pelabuhan Transit Kosong</div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="kodePelTujuanText" class="form-label">Pelabuhan Tujuan</label>
                                            <input type="text" class="form-control" id="kodePelTujuanText" spellcheck="false" autocomplete="off" placeholder="" disabled>
                                        </div>
                                        <div class="mb-3">
                                            <label for="kodeTps" class="form-label">Tempat Penimbunan</label>
                                            <select class="select2" name="kodeTps" id="kodeTps" aria-describedby="kodeTpsFeedback">
                                                <option></option>
                                            </select>
                                            <div id="kodeTpsFeedback" class="invalid-feedback">Tempat Penimbunan Kosong</div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="pills-kemasan" role="tabpanel" aria-labelledby="pills-kemasan-tab" tabindex="4">...</div>
                    <div class="tab-pane fade" id="pills-transaksi" role="tabpanel" aria-labelledby="pills-transaksi-tab" tabindex="5">
                        <div class="row">
                            <div class="col-sm-4 mb-3 mb-sm-0">                          
                                <div class="card mb-4">
                                    <div class="card-header" style="border-bottom: var(--cui-card-border-width) solid var(--cui-card-border-color); background-color: var(--cui-gray-200)">
                                        <h6 class="card-title text-body-secondary">Harga</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="kodeValuta" class="form-label">Valuta</label>
                                            <select class="select2" name="kodeValuta" id="kodeValuta" aria-describedby="kodeValutaFeedback">
                                                <option></option>
                                            </select>
                                            <div id="kodeValutaFeedback" class="invalid-feedback">Valuta Kosong</div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="ndpbm" class="form-label">NDPBM</label>
                                            <input type="text" class="form-control" name="ndpbm" id="ndpbm" spellcheck="false" autocomplete="off" placeholder="" value="0.0000" disabled>
                                        </div>
                                        <div class="mb-3">
                                            <label for="kodeJenisNilai" class="form-label">Jenis Transaksi</label>
                                            <select class="select2" name="kodeJenisNilai" id="kodeJenisNilai" aria-describedby="kodeJenisNilaiFeedback">
                                                <option></option>
                                            </select>
                                            <div id="kodeJenisNilaiFeedback" class="invalid-feedback">Jenis Transaksi Kosong</div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="kodeJenisNilaix" class="form-label">Harga Barang</label>
                                            <div class="input-group mb-3">
                                                <div class="col-sm-5">
                                                    <select class="select2" name="kodeJenisNilaix" id="kodeJenisNilaix" aria-describedby="kodeJenisNilaiFeedback">
                                                        <option></option>
                                                    </select>
                                                    <div id="kodeJenisNilaiFeedback" class="invalid-feedback">Kode Harga Kosong</div>
                                                </div>
                                                <div class="col-sm-7" style="padding-left: 5px;">
                                                    <input type="text" class="form-control border-top-left-radius border-bottom-left-radius" name="nomorBc11" id="nomorBc11" spellcheck="false" autocomplete="off" placeholder="" value="0.00">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="cif" class="form-label">Nilai Pabean</label>
                                            <input type="text" class="form-control" name="cif" id="cif" spellcheck="false" autocomplete="off" placeholder="" value="0.00" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4 mb-3 mb-sm-0">                          
                                <div class="card mb-4">
                                    <div class="card-header" style="border-bottom: var(--cui-card-border-width) solid var(--cui-card-border-color); background-color: var(--cui-gray-200)">
                                        <h6 class="card-title text-body-secondary">Harga Lainnya</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="biayaTambahan" class="form-label">Biaya Penambah</label>
                                            <input type="text" class="form-control" name="biayaTambahan" id="biayaTambahan" spellcheck="false" autocomplete="off" placeholder="" value="0.00">
                                        </div>
                                        <div class="mb-3">
                                            <label for="biayaPengurang" class="form-label">Biaya Pengurang</label>
                                            <input type="text" class="form-control" name="biayaPengurang" id="biayaPengurang" spellcheck="false" autocomplete="off" placeholder="" value="0.00">
                                        </div>
                                        <div class="mb-3">
                                            <label for="ndpbm" class="form-label">Freight</label>
                                            <input type="text" class="form-control" name="ndpbm" id="ndpbm" spellcheck="false" autocomplete="off" placeholder="" value="0.00">
                                        </div>
                                        <div class="mb-3">
                                            <label for="kodeJenisNilaix" class="form-label">Asuransi</label>
                                            <div class="input-group mb-3">
                                                <div class="col-sm-5">
                                                    <select class="select2" name="kodeJenisNilaix" id="kodeJenisNilaix" aria-describedby="kodeJenisNilaiFeedback">
                                                        <option></option>
                                                    </select>
                                                    <div id="kodeJenisNilaiFeedback" class="invalid-feedback">Kode Harga Kosong</div>
                                                </div>
                                                <div class="col-sm-7" style="padding-left: 5px;">
                                                    <input type="text" class="form-control border-top-left-radius border-bottom-left-radius" name="nomorBc11" id="nomorBc11" spellcheck="false" autocomplete="off" placeholder="" value="0.00">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="vd" class="form-label">Voluntary Declaration</label>
                                            <input type="text" class="form-control" name="vd" id="vd" spellcheck="false" autocomplete="off" placeholder="" value="0.00">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4 mb-3 mb-sm-0">                          
                                <div class="card mb-4">
                                    <div class="card-header" style="border-bottom: var(--cui-card-border-width) solid var(--cui-card-border-color); background-color: var(--cui-gray-200)">
                                        <h6 class="card-title text-body-secondary">Berat</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="bruto" class="form-label">Berat Kotor (KGM)</label>
                                            <input type="text" class="form-control" name="bruto" id="bruto" spellcheck="false" autocomplete="off" placeholder="" value="0.0000" aria-describedby="brutoFeedback">
                                            <div id="brutoFeedback" class="invalid-feedback">Berat Kotor harus lebih besar dari 0</div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="netto" class="form-label">Berat Bersih (KGM)</label>
                                            <input type="text" class="form-control" name="netto" id="netto" spellcheck="false" autocomplete="off" placeholder="" value="0.0000" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="pills-barang" role="tabpanel" aria-labelledby="pills-barang-tab" tabindex="6">...</div>
                    <div class="tab-pane fade" id="pills-pungutan" role="tabpanel" aria-labelledby="pills-pungutan-tab" tabindex="7">...</div>
                    <div class="tab-pane fade" id="pills-persyaratan" role="tabpanel" aria-labelledby="pills-persyaratan-tab" tabindex="8">
                        <div class="row">
                            <div class="col-sm-6 mb-3 mb-sm-0">                          
                                <div class="card mb-4">
                                    <div class="card-header" style="border-bottom: var(--cui-card-border-width) solid var(--cui-card-border-color); background-color: var(--cui-gray-200)">
                                        <h6 class="card-title text-body-secondary">Tempat & Tanggal</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="kotaTtd" class="form-label">Tempat</label>
                                            <input type="text" class="form-control" name="kotaTtd" id="kotaTtd" spellcheck="false" autocomplete="off" placeholder="" aria-describedby="kotaTtdFeedback">
                                            <div id="kotaTtdFeedback" class="invalid-feedback">Tempat kosong</div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="tanggalTtd" class="form-label">Tanggal</label>
                                            <div id="tanggalTtd"></div>
                                            <div id="tanggalTtdFeedback" class="invalid-feedback">Tanggal kosong</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-3 mb-sm-0">                          
                                <div class="card mb-4">
                                    <div class="card-header" style="border-bottom: var(--cui-card-border-width) solid var(--cui-card-border-color); background-color: var(--cui-gray-200)">
                                        <h6 class="card-title text-body-secondary">Nama</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="namaTtd" class="form-label">Nama</label>
                                            <input type="text" class="form-control" name="namaTtd" id="namaTtd" spellcheck="false" autocomplete="off" placeholder="" aria-describedby="namaTtdFeedback">
                                            <div id="namaTtdFeedback" class="invalid-feedback">Nama kosong</div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="jabatanTtd" class="form-label">Jabatan</label>
                                            <input type="text" class="form-control" name="jabatanTtd" id="jabatanTtd" spellcheck="false" autocomplete="off" placeholder="" aria-describedby="jabatanTtdFeedback">
                                            <div id="jabatanTtdFeedback" class="invalid-feedback">Jabatan kosong</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="modalNewDocument" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="">Dokumen Baru</h5>
                    <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label for="entitas" class="col-form-label">Entitas:</label>
                            <select class="form-select" aria-label="" id="entitas" name="entitas">
                                <option value="PPJK" selected>PPJK</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="message-text" class="col-form-label">Jenis Dokumen:</label>
                            <select class="form-select" aria-label="" id="entitas" name="entitas">
                                <option></option>
                                <option value="BC20" selected>BC 2.0</option>
                                <option value="BC30">BC 3.0</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-info">Next</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('extra_js')
    <script>
        $('.autosize').autosize({ append: "\n" });
        $('.select2').select2({ allowClear: true, placeholder: '-- Select --'});
        window.scrollTo({ top: 0, behavior: 'smooth' });
        var accessToken, authType = null;
        setTimeout(function () {
            $('#sidebar').addClass('sidebar-narrow-unfoldable');
        }, 700);
        $('.form-control').val(null).trigger('change');
        $('.select2').val(null).trigger('change');
        $('#example').DataTable();

        async function ceisaAuth() {
            Snackbar.show({ text: 'Connecting to CEISA 4.0 gateway' });
            try {
                const response = await fetch('/ceisah2h/dokumen_pabean/ceisa_auth', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                });

                const data = await response.json();
                if (response.ok) {
                    accessToken = data['accessToken'];
                    authType = data['authType'];
                    Snackbar.show({ text: data['message'] });
                } else {
                    Snackbar.show({ text: data['message'] });
                }
            } catch (error) {
                console.error('Error:', error);
                Snackbar.show({ text: 'An error occurred while fetching data.' });
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            dayjs.locale('id');
            dayjs.extend(window.dayjs_plugin_customParseFormat);
            const optionsDatePicker = {
                locale: 'id-ID',
                inputDateFormat: date => dayjs(date).locale('id').format('DD-MM-YYYY'),
                inputDateParse: date => dayjs(date, 'DD-MM-YYYY', 'id').toDate(),
            }       
        
            const tanggalBc11 = document.getElementById('tanggalBc11');
            new coreui.DatePicker(document.getElementById('tanggalBc11'), optionsDatePicker);
            new coreui.DatePicker(document.getElementById('tanggalTiba'), optionsDatePicker);
            new coreui.DatePicker(document.getElementById('tanggalTtd'), optionsDatePicker);
        });
        
        $(document).ready(function() {
            ceisaAuth();

            $('#kodePelTujuan').select2({
                minimumInputLength: 3,
                allowClear: true,
                placeholder: '-- Select --',
                ajax: {
                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                    dataType: 'json',
                    url : '/ceisah2h/dokumen_pabean/get_data_form',
                    delay: 250,
                    type: 'POST',
                    data: function(params) {
                        return {
                            search: params.term,
                            dataType: 'kodePelabuhan',
                            dataSrc: 'db', 
                            page: params.page || 1
                        }
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            // results: data
                            results: data.map(item => ({
                                id: item.id,
                                text: item.text,
                                title: item.text,
                            })),
                            pagination: {
                                more: (params.page * 10) < data.count_filtered
                            }
                        }
                    },
                }
            });

            $(document).on('change', '#kodePelTujuan', function (event) {
                const $this = $(this);
                $('#kodePelTujuanText').val($('#kodePelTujuan option:selected').text());
                $('#kodeKantor').html('').val(null).trigger('change');
                if($this.val()){
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                        dataType: 'json',
                        url: '/ceisah2h/dokumen_pabean/get_data_form',
                        type: 'POST',
                        data: {
                                dataType: 'getKantor',
                                dataSrc: 'db',
                                dataValue: $this.val(),
                            },
                        success: function (response) {                        
                            response.forEach(function(item) {
                                var isSelected = response.length === 1;
                                var newOption = new Option(item.text, item.id, isSelected, isSelected);
                                $('#kodeKantor').append(newOption).trigger('change');
                            });
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            return false;
                        }
                    });
                }
            });

            $('.selectNegara').select2({
                minimumInputLength: 2,
                allowClear: true,
                placeholder: '-- Select --',
                ajax: {
                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                    dataType: 'json',
                    url : '/ceisah2h/dokumen_pabean/get_data_form',
                    delay: 250,
                    type: 'POST',
                    data: function(params) {
                        return {
                            search: params.term,
                            dataType: 'namaNegara',
                            dataSrc: 'db', 
                            page: params.page || 1
                        }
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            // results: data
                            results: data.map(item => ({
                                id: item.id,
                                text: item.text,
                                title: item.text,
                            })),
                            pagination: {
                                more: (params.page * 10) < data.count_filtered
                            }
                        }
                    },
                }
            });

            $(document).on('change', '.kodeJenisIdentitas', function (e) {
                let $this = $(this);
                let parent = $this.parent().parent();
                let cardContainer = parent.parent().parent();

                parent.find('input.nomorIdentitas').removeClass('formatNpwp15');
                parent.find('input.nomorIdentitas').val(null).trigger('change');
                if($this.val() === '5'){
                    parent.find('input.nomorIdentitas').addClass('formatNpwp15');
                }
            });

            $(document).on('click', '.salinEntitas', function (e) {
                let $this = $(this);
                let src = $this.attr('data-src');
                let dest = $this.attr('data-dest');

                // let nomorIdentias = ("" + $(`#nomorIdentitas${src}`).val()).replace(/\D/g, "");                
                $(`#kodeJenisIdentitas${dest}`).val($(`#kodeJenisIdentitas${src}`).val()).trigger('change');
                $(`#nomorIdentitas${dest}`).val($(`#nomorIdentitas${src}`).val());
                $(`#namaEntitas${dest}`).val($(`#namaEntitas${src}`).val()).trigger('change');
                $(`#alamatEntitas${dest}`).val($(`#alamatEntitas${src}`).val()).trigger('change');
                $(`#kodeJenisApi${dest}`).val($(`#kodeJenisApi${src}`).val()).trigger('change');
                $(`#nibEntitas${dest}`).val($(`#nibEntitas${src}`).val()).trigger('change');
                $(`#statusEntitas${dest}`).val($(`#statusEntitas${src}`).val()).trigger('change');
                $(`#namaNegara${dest}`).val($(`#namaNegara${src}`).val()).trigger('change');
            });

            function autoFormatNPWP(NPWPString) {
                try {
                    var cleaned = ("" + NPWPString).replace(/\D/g, "");
                    var match = cleaned.match(/(\d{0,2})?(\d{0,3})?(\d{0,3})?(\d{0,1})?(\d{0,3})?(\d{0,3})$/);
                    return [      
                            match[1], 
                            match[2] ? ".": "",
                            match[2], 
                            match[3] ? ".": "",
                            match[3],
                            match[4] ? ".": "",
                            match[4],
                            match[5] ? "-": "",
                            match[5],
                            match[6] ? ".": "",
                            match[6]].join("")
                    
                } catch(err) {
                    return "";
                }
            }

            $(document).on('input', '.formatNpwp15', function (e) {
                $(this).val(autoFormatNPWP($(this).val()));
            });

            $(document).on('change', '.nomorIdentitas', function (e) {
                let $this = $(this);
                const parent = $this.parent().parent();
                const cardContainer = parent.parent().parent();

                const kodeJenisIdentitasComponent = parent.find('select.kodeJenisIdentitas');
                const namaEntitasComponent = cardContainer.find('input.namaEntitas');
                const alamatEntitasComponent = cardContainer.find('textarea.alamatEntitas');
                const kodeJenisApiComponent = cardContainer.find('select.kodeJenisApi');
                const nibEntitasComponent = cardContainer.find('input.nibEntitas');

                namaEntitasComponent.prop('disabled', false);
                alamatEntitasComponent.prop('disabled', false);
                namaEntitasComponent.val('').trigger('change');
                alamatEntitasComponent.val('').trigger('change');
                kodeJenisApiComponent.val(null).trigger('change');
                nibEntitasComponent.val('').trigger('change');

                if(kodeJenisIdentitasComponent.val() === '5' && $this.val() != ''){
                    namaEntitasComponent.val('Please wait...').trigger('change');
                    alamatEntitasComponent.val('Please wait...').trigger('change');
                    namaEntitasComponent.prop('disabled', true);
                    alamatEntitasComponent.prop('disabled', true);

                    $.ajax({
                        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                        dataType: 'json',
                        url: '/ceisah2h/dokumen_pabean/get_data_form',
                        type: 'POST',
                        data: {
                                dataType: kodeJenisIdentitasComponent.val(),
                                dataSrc: 'api',
                                dataValue: ("" + $this.val()).replace(/\D/g, ""),
                            },
                        success: function (response) {
                            namaEntitasComponent.prop('disabled', false);
                            alamatEntitasComponent.prop('disabled', false);
                            namaEntitasComponent.trigger('change');
                            alamatEntitasComponent.trigger('change');
                            if(response.status){
                                namaEntitasComponent.val(response['identitas']['namaPerusahaan']).trigger('change');
                                alamatEntitasComponent.val(response['identitas']['alamatPerusahaan']).trigger('change');
                                nibEntitasComponent.val(response['identitas']['nib']).trigger('change');
                                if(response['identitas']['jenisApi'] != null){
                                    kodeJenisApiComponent.val(response['identitas']['jenisApi']).trigger('change');
                                }
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            namaEntitasComponent.prop('disabled', false);
                            alamatEntitasComponent.prop('disabled', false);
                            namaEntitasComponent.val('').trigger('change');
                            alamatEntitasComponent.val('').trigger('change');
                            // var error = textStatus + ' ' + jqXHR.status + ' ' + jqXHR.statusText;
                            // alert(error.toUpperCase() + '\n' + jqXHR.responseText);
                            return false;
                        }
                    });
                }

                // async function getNpwp() {
                //     try {
                //         const response = await fetch('https://apis-gw.beacukai.go.id/v2/sce-ws/profil/perusahaan/perusahaanib-by-npwp9?npwp9='+$this.val(), {
                //             method: 'GET',
                //             headers: {
                //                 'Content-Type': 'application/json',
                //                 'Authorization': 'Bearer '+accessToken
                //             },
                //         });

                //         const data = await response.json();
                //         if (response.ok) {
                //             $('#nama_entitas_importir').val(data['nmNpwp']).trigger('change');
                //             $('#alamat_entitas_importir').val(data['alNpwp']).trigger('change');
                //         }
                //         // else {
                //         //     Snackbar.show({ text: data['message'] });
                //         // }
                //     } catch (error) {
                //         console.error('Error:', error);
                //         Snackbar.show({ text: 'An error occurred while fetching data.' });
                //     }
                // }

                // getNpwp();
            });
        });
        
    </script>
@endsection
