<input type="hidden" id="max-file-size" value="{{ $settings->upload->maxFileSize }}"/>
<input type="hidden" id="max-photo-size" value="{{ $settings->upload->maxPhotoSize }}"/>

{{ js::i18n([
'date_interval_y_singular',
'date_interval_y_plural',
'date_interval_m_singular',
'date_interval_m_plural',
'date_interval_d_singular',
'date_interval_d_plural',
'date_interval_h_singular',
'date_interval_h_plural',
'date_interval_i_singular',
'date_interval_i_plural',
'date_interval_seconds_ago'], 'dateDifferenceI18n'); }}