<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ccx extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        if (! (is_admin() || has_permission('reports', '', 'view') || has_permission('invoices', '', 'view'))) {
            access_denied('CCX');
        }

        $this->load->model('ccx/ccx_model');
    }

    public function index()
    {
        redirect(admin_url('ccx/reports'));
    }

    public function reports()
    {
        $data['title']    = ccx_lang('ccx_reports_page_title', 'Reports');
        $data['sections'] = $this->ccx_model->get_sections_overview();

        $this->load->view('ccx/reports/index', $data);
    }

    public function report($id)
    {
        $id = (int) $id;
        if ($id <= 0) {
            show_404();
        }

        $template = $this->ccx_model->get_template($id);
        if (! $template) {
            set_alert('warning', ccx_lang('ccx_template_not_found', 'Template not found.'));
            redirect(admin_url('ccx/reports'));
        }

        if ($this->ccx_model->is_sql_template($template)) {
            $filterDefinitions = $this->decode_saved_filters($template['filters'] ?? null);
            $filterContext     = $this->build_filter_context($filterDefinitions);

            $result = [
                'columns'   => [],
                'rows'      => [],
                'row_limit' => null,
            ];
            $error = null;

            if ($filterContext['has_errors']) {
                $error = ccx_lang('ccx_template_sql_filters_invalid', 'Filters could not be applied. Please review the highlighted fields.');
            } else {
                [$result, $error] = $this->ccx_model->run_sql_template_query($template, $filterContext['values']);
                if ($error !== null) {
                    set_alert('danger', $error);
                }
            }

            $data['title']               = ccx_lang('ccx_reports_sql_view_title', 'View Report');
            $data['template']            = $template;
            $data['columns']             = $result['columns'] ?? [];
            $data['rows']                = $result['rows'] ?? [];
            $data['row_limit']           = $result['row_limit'] ?? null;
            $data['query_error']         = $error;
            $data['filters']             = $filterContext['filters'];
            $data['filters_applied']     = $filterContext['values'];
            $data['filters_has_errors']  = $filterContext['has_errors'];
            $data['filters_submitted']   = $filterContext['submitted'];

            $this->load->view('ccx/reports/sql', $data);

            return;
        }

        $filterDefinitions = $this->decode_saved_filters($template['filters'] ?? null);
        $filterContext     = $this->build_filter_context($filterDefinitions);

        if (! empty($filterContext['values'])) {
            $_GET['filters'] = $filterContext['values'];
        }

        $columns    = $this->ccx_model->get_template_columns($template['id']);
        $tableSlug  = 'ccx-template-' . $template['id'];
        $table      = $this->ccx_model->ensure_template_table($template, $columns);

        $data['title']      = ccx_lang('ccx_reports_view_page_title', 'View Report');
        $data['template']   = $template;
        $data['columns']    = $columns;
        $data['tableSlug']  = $tableSlug;
        $data['table']      = $table;
        $data['filters']    = $filterContext['filters'];
        $data['filters_applied'] = $filterContext['values'];
        $data['filters_has_errors'] = $filterContext['has_errors'];
        $data['filters_submitted']  = $filterContext['submitted'];

        $this->load->view('ccx/reports/detail', $data);
    }

    public function report_table($id)
    {
        if (! $this->input->is_ajax_request()) {
            show_error('No direct script access allowed');
        }

        $id = (int) $id;
        if ($id <= 0) {
            show_404();
        }

        $template = $this->ccx_model->get_template($id);
        if (! $template) {
            show_404();
        }

        if ($this->ccx_model->is_sql_template($template)) {
            show_404();
        }

        $options = [
            'draw'    => (int) ($this->input->post('draw') ?? 0),
            'search'  => trim($this->input->post('search')['value'] ?? ''),
            'filters' => $this->input->post('filters') ?? [],
            'start'   => (int) ($this->input->post('start') ?? 0),
            'length'  => $this->input->post('length') !== null ? (int) $this->input->post('length') : null,
            'date_from' => $this->input->post('date_from'),
            'date_to'   => $this->input->post('date_to'),
            'runtime_filters' => $this->input->post('runtime_filters') ?? [],
        ];

        $filterDefinitions = $this->decode_saved_filters($template['filters'] ?? null);
        $runtimeFiltersInput = $options['runtime_filters'];
        if (! is_array($runtimeFiltersInput)) {
            $runtimeFiltersInput = [];
        }

        $runtimeContext = $this->build_filter_context($filterDefinitions, $runtimeFiltersInput, true);
        if ($runtimeContext['has_errors']) {
            $empty = [
                'draw'                 => $options['draw'],
                'recordsTotal'         => 0,
                'recordsFiltered'      => 0,
                'iTotalRecords'        => 0,
                'iTotalDisplayRecords' => 0,
                'aaData'               => [],
            ];
            echo json_encode($empty);
            return;
        }

        $options['runtime_filters'] = $runtimeContext['values'];
        $_POST['runtime_filters'] = $runtimeContext['values'];

        try {
            $response = $this->ccx_model->get_template_table_data($id, $options);
            $json     = json_encode($response);
            if ($json === false) {
                log_message('error', 'CCX report_table JSON encoding failed: ' . json_last_error_msg());
                $json = json_encode([
                    'draw'                 => (int) ($options['draw'] ?? 0),
                    'recordsTotal'         => 0,
                    'recordsFiltered'      => 0,
                    'iTotalRecords'        => 0,
                    'iTotalDisplayRecords' => 0,
                    'aaData'               => [],
                    'error'                => 'Encoding failure',
                ]);
            }
            header('Content-Type: application/json; charset=utf-8');
            echo $json;
        } catch (Throwable $e) {
            log_message('error', 'CCX report_table failure: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            if (method_exists($this, 'output')) {
                $this->output->set_status_header(500);
            } else {
                header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error');
            }
            echo json_encode([
                'error' => 'Unable to render report dataset.',
            ]);
        }
        exit;
    }

    public function templates()
    {
        $data['title']     = ccx_lang('ccx_templates_page_title', 'Report Templates');
        $data['templates'] = $this->ccx_model->get_templates();

        $this->load->view('ccx/templates/list', $data);
    }

    public function template($id = null)
    {
        $id = $id !== null ? (int) $id : null;

        if ($this->input->method() === 'post') {
            $template   = $this->input->post('template') ?? [];
            $type       = strtolower(trim((string) ($template['type'] ?? 'smart')));
            if (! in_array($type, ['smart', 'sql'], true)) {
                $type = 'smart';
            }
            $template['type'] = $type;

            $filtersJsonInput = (string) $this->input->post('filters_json');
            [$filters, $filterErrors] = $this->parse_filters_json($filtersJsonInput);
            $filtersJsonStored = ! empty($filters) ? json_encode($filters, JSON_UNESCAPED_UNICODE) : null;

            $normalizedQuery = '';
            if ($type === 'sql') {
                $rawQuery = (string) $this->input->post('sql_query');
                $normalizedQuery = html_entity_decode($rawQuery, ENT_QUOTES | ENT_HTML5);
                $normalizedQuery = str_replace(["\r\n", "\r"], "\n", $normalizedQuery);
            }

            if (! empty($filterErrors)) {
                set_alert('danger', implode('<br>', $filterErrors));
                $this->session->set_flashdata('ccx_template_filters_json', $filtersJsonInput);
                $this->session->set_flashdata('ccx_template_selected_type', $type);
                if ($type === 'sql') {
                    $this->session->set_flashdata('ccx_template_sql_query', $normalizedQuery);
                }
                redirect(admin_url('ccx/template' . ($id ? '/' . $id : '')));
            }

            $template['filters'] = $filtersJsonStored;

            if ($type === 'sql') {
                if (trim((string) ($template['name'] ?? '')) === '' || trim($normalizedQuery) === '') {
                    set_alert('danger', ccx_lang('ccx_template_sql_required', 'Template name and SQL query are required.'));
                    $this->session->set_flashdata('ccx_template_filters_json', $filtersJsonInput);
                    $this->session->set_flashdata('ccx_template_selected_type', 'sql');
                    $this->session->set_flashdata('ccx_template_sql_query', $normalizedQuery);
                    redirect(admin_url('ccx/template' . ($id ? '/' . $id : '')));
                }

                if (! $this->ccx_model->is_safe_sql_query($normalizedQuery)) {
                    set_alert('danger', ccx_lang('ccx_template_sql_invalid', 'Only read-only SQL statements are allowed.'));
                    $this->session->set_flashdata('ccx_template_filters_json', $filtersJsonInput);
                    $this->session->set_flashdata('ccx_template_selected_type', 'sql');
                    $this->session->set_flashdata('ccx_template_sql_query', $normalizedQuery);
                    redirect(admin_url('ccx/template' . ($id ? '/' . $id : '')));
                }

                $columns    = [];
                $sqlPayload = [
                    'sql_query' => $normalizedQuery,
                    'filters'   => $filtersJsonStored,
                    'is_active' => $this->input->post('is_active') ? 1 : 0,
                ];

                $templateId = $this->ccx_model->save_template($id, $template, $columns, $sqlPayload);
            } else {
                $columns    = $this->input->post('columns') ?? [];
                $templateId = $this->ccx_model->save_template($id, $template, $columns);
            }

            if ($templateId) {
                set_alert('success', ccx_lang('ccx_template_saved', 'Template saved successfully.'));
                redirect(admin_url('ccx/templates'));
            }

            set_alert('danger', ccx_lang('ccx_template_save_failed', 'Unable to save the template, please review the input.'));
            redirect(admin_url('ccx/template' . ($id ? '/' . $id : '')));
        }

        $tablesMeta            = $this->ccx_model->get_available_tables();
        $tableOptions          = [];
        foreach ($tablesMeta as $short => $info) {
            $tableOptions[$short] = $info['label'] ?? $short;
        }

        $templateRecord = $id ? $this->ccx_model->get_template($id) : null;
        if ($id && ! $templateRecord) {
            set_alert('warning', ccx_lang('ccx_template_not_found', 'Template not found.'));
            redirect(admin_url('ccx/templates'));
        }

        $selectedType = $templateRecord ? strtolower((string) ($templateRecord['type'] ?? 'smart')) : 'smart';
        if (! in_array($selectedType, ['smart', 'sql'], true)) {
            $selectedType = 'smart';
        }

        $flashSelectedType = $this->session->flashdata('ccx_template_selected_type');
        if ($flashSelectedType !== null) {
            $flashSelectedType = strtolower(trim((string) $flashSelectedType));
            if (in_array($flashSelectedType, ['smart', 'sql'], true)) {
                $selectedType = $flashSelectedType;
            }
        }

        $columns = [];
        if ($selectedType === 'smart' && $id) {
            $columns = $this->ccx_model->get_template_columns($id);
        }

        $sqlTemplate = [
            'sql_query' => $templateRecord ? (string) ($templateRecord['sql_query'] ?? '') : '',
            'is_active' => $templateRecord ? (int) ($templateRecord['is_active'] ?? 1) : 1,
        ];

        $flashSqlQuery = $this->session->flashdata('ccx_template_sql_query');
        if ($flashSqlQuery !== null) {
            $sqlTemplate['sql_query'] = (string) $flashSqlQuery;
        }

        $storedFilters   = $templateRecord['filters'] ?? null;
        $templateFilters = $this->decode_saved_filters($storedFilters);
        $prefillFiltersJson = $this->session->flashdata('ccx_template_filters_json');
        if ($prefillFiltersJson !== null) {
            $filtersJson = $prefillFiltersJson;
        } elseif (! empty($templateFilters)) {
            $filtersJson = json_encode($templateFilters, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } else {
            $filtersJson = '';
        }

        $data['title']         = $id ? ccx_lang('ccx_template_edit_title', 'Edit Report Template') : ccx_lang('ccx_template_create_title', 'Create Report Template');
        $data['template']      = $templateRecord;
        $data['templateType']  = $selectedType;
        $data['columns']       = $columns;
        $data['aggregateMap'] = $this->ccx_model->get_aggregate_options();
        $data['tableOptions'] = $tableOptions;
        $data['columnsMap']   = $this->ccx_model->get_columns_map($tablesMeta);
        $data['sqlTemplate']   = $sqlTemplate;
        $data['templateFilters'] = $templateFilters;
        $data['filtersJson']   = $filtersJson;

        $this->load->view('ccx/templates/form', $data);
    }

    public function template_preview()
    {
        if ($this->input->method() !== 'post') {
            show_404();
        }

        $columnInput = $this->input->post('column', false);
        if ($columnInput !== null) {
            if (is_string($columnInput)) {
                $decoded = json_decode($columnInput, true);
                $columnInput = is_array($decoded) ? $decoded : null;
            } elseif (! is_array($columnInput)) {
                $columnInput = null;
            }
        } else {
            $payload = json_decode($this->input->raw_input_stream, true);
            if (! is_array($payload)) {
                $payload = [];
            }
            $columnInput = $payload['column'] ?? null;
        }
        if (! is_array($columnInput)) {
            $response = [
                'success' => false,
                'state'   => 'invalid',
                'message' => ccx_lang('ccx_template_preview_invalid', 'Unable to build a preview with the current inputs.'),
            ];
        } else {
            $response = $this->ccx_model->preview_column($columnInput);
        }

        $status = 200;
        if (empty($response['success'])) {
            $status = ($response['state'] ?? '') === 'error' ? 500 : 422;
        }

        $json = json_encode($response);
        if ($json === false) {
            log_message('error', 'CCX template_preview JSON encoding failed: ' . json_last_error_msg());
            $json = json_encode([
                'success' => false,
                'state'   => 'error',
                'message' => 'Encoding failure',
            ]);
            $status = 500;
        }

        if (method_exists($this, 'output')) {
            $this->output->set_content_type('application/json; charset=utf-8');
            $this->output->set_status_header($status);
            $this->output->set_output($json);
        } else {
            $statusHeader = $_SERVER['SERVER_PROTOCOL'] . ' ' . $status;
            header('Content-Type: application/json; charset=utf-8');
            header($statusHeader);
            echo $json;
        }
        exit;
    }

    public function delete_template($id)
    {
        $id = (int) $id;
        if ($id <= 0) {
            show_404();
        }

        if ($this->ccx_model->delete_template($id)) {
            set_alert('success', ccx_lang('ccx_template_deleted', 'Template deleted.'));
        } else {
            set_alert('danger', ccx_lang('ccx_template_delete_failed', 'Unable to delete template.'));
        }

        redirect(admin_url('ccx/templates'));
    }

    public function sections()
    {
        $data['title']    = ccx_lang('ccx_sections_page_title', 'Report Sections');
        $data['sections'] = $this->ccx_model->get_sections();

        $this->load->view('ccx/sections/list', $data);
    }

    public function section($id = null)
    {
        $id = $id !== null ? (int) $id : null;

        if ($this->input->method() === 'post') {
            $section     = $this->input->post('section') ?? [];
            $templateIds = $this->input->post('template_ids') ?? [];

            $sectionId = $this->ccx_model->save_section($id, $section, $templateIds);

            if ($sectionId) {
                set_alert('success', ccx_lang('ccx_section_saved', 'Section saved successfully.'));
                redirect(admin_url('ccx/sections'));
            }

            set_alert('danger', ccx_lang('ccx_section_save_failed', 'Unable to save the section, please review the input.'));
            redirect(admin_url('ccx/section' . ($id ? '/' . $id : '')));
        }

        $data['title']       = $id ? ccx_lang('ccx_section_edit_title', 'Edit Report Section') : ccx_lang('ccx_section_create_title', 'Create Report Section');
        $data['section']     = $id ? $this->ccx_model->get_section($id) : null;
        $data['templates']   = $this->ccx_model->get_templates();
        $data['selectedIds'] = $id ? $this->ccx_model->get_section_template_ids($id) : [];

        if ($id && ! $data['section']) {
            set_alert('warning', ccx_lang('ccx_section_not_found', 'Section not found.'));
            redirect(admin_url('ccx/sections'));
        }

        $this->load->view('ccx/sections/form', $data);
    }

    public function import_export()
    {
        $data['title']         = ccx_lang('ccx_import_export_page_title', 'Import & Export');
        $data['exportUrl']     = admin_url('ccx/export_bundle');
        $data['importAction']  = admin_url('ccx/import_bundle');
        $data['templatesList'] = $this->ccx_model->get_templates();

        $this->load->view('ccx/import_export/index', $data);
    }

    public function export_bundle()
    {
        if ($this->input->method() !== 'get') {
            show_404();
        }

        $templateId = (int) ($this->input->get('template_id') ?? 0);
        $templateIds = null;

        if ($templateId > 0) {
            $template = $this->ccx_model->get_template($templateId);
            if (! $template) {
                set_alert('danger', ccx_lang('ccx_template_not_found', 'Template not found.'));
                redirect(admin_url('ccx/import_export'));
            }

            $templateIds = [$templateId];
        }

        $bundle = $this->ccx_model->get_export_bundle($templateIds);
        $json   = json_encode($bundle, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if ($json === false) {
            log_message('error', 'CCX export_bundle json_encode failed: ' . json_last_error_msg());
            set_alert('danger', ccx_lang('problem_exporting_json', 'Failed to generate the export bundle.'));
            redirect(admin_url('ccx/import_export'));
        }

        $filename = 'ccx-export-' . date('Ymd-His') . '.json';

        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo $json;
        exit;
    }

    public function import_bundle()
    {
        if ($this->input->method() !== 'post') {
            show_404();
        }

        $file       = $_FILES['import_file'] ?? null;
        $contents   = '';
        $pastedJson = $this->input->post('import_json', false);

        if (is_string($pastedJson)) {
            $contents = trim($pastedJson);
        }

        if ($file && isset($file['tmp_name']) && is_uploaded_file($file['tmp_name'])) {
            $fileContents = (string) file_get_contents($file['tmp_name']);
            if (trim($fileContents) !== '') {
                $contents = $fileContents;
            }
        }

        if (trim($contents) === '') {
            set_alert('danger', ccx_lang('ccx_import_export_no_input', 'Provide a JSON file or paste JSON to import.'));
            redirect(admin_url('ccx/import_export'));
        }

        $payload = json_decode($contents, true);
        if (! is_array($payload)) {
            log_message('error', 'CCX import_bundle json_decode failed: ' . json_last_error_msg());
            set_alert('danger', ccx_lang('ccx_import_export_decode_failed', 'Unable to decode the uploaded JSON file.'));
            redirect(admin_url('ccx/import_export'));
        }

        $result = $this->ccx_model->import_bundle($payload);

        if (empty($result['success'])) {
            set_alert('danger', ccx_lang('ccx_import_export_invalid_bundle', 'The uploaded file is not a valid CCX export bundle.'));
        } else {
            $templates = isset($result['templates']) ? (int) $result['templates'] : 0;
            $sections  = isset($result['sections']) ? (int) $result['sections'] : 0;
            set_alert('success', sprintf(ccx_lang('ccx_import_export_import_success', 'Imported %d templates and %d sections.'), $templates, $sections));
        }

        redirect(admin_url('ccx/import_export'));
    }

    public function delete_section($id)
    {
        $id = (int) $id;
        if ($id <= 0) {
            show_404();
        }

        if ($this->ccx_model->delete_section($id)) {
            set_alert('success', ccx_lang('ccx_section_deleted', 'Section deleted.'));
        } else {
            set_alert('danger', ccx_lang('ccx_section_delete_failed', 'Unable to delete section.'));
        }

        redirect(admin_url('ccx/sections'));
    }

    /**
     * @param string $filtersJson
     *
     * @return array{0: array<int,array<string,mixed>>, 1: array<int,string>}
     */
    private function parse_filters_json(string $filtersJson): array
    {
        $filtersJson = trim($filtersJson);
        if ($filtersJson === '') {
            return [[], []];
        }

        $decoded = json_decode($filtersJson, true);
        if (! is_array($decoded)) {
            return [[], [ccx_lang('ccx_template_sql_filter_invalid_json', 'Filters JSON must decode to an array.')]];
        }

        $normalized = [];
        $errors     = [];
        $seenKeys   = [];

        foreach ($decoded as $index => $rawFilter) {
            $position = $index + 1;

            if (! is_array($rawFilter)) {
                $errors[] = sprintf(ccx_lang('ccx_template_sql_filter_invalid_object', 'Filter definition #%d must be an object.'), $position);
                continue;
            }

            $key = isset($rawFilter['key']) ? trim((string) $rawFilter['key']) : '';
            if ($key === '') {
                $errors[] = sprintf(ccx_lang('ccx_template_sql_filter_key_required', 'Filter definition #%d requires a "key" value.'), $position);
                continue;
            }

            if (! preg_match('/^[A-Za-z0-9_]+$/', $key)) {
                $errors[] = sprintf(ccx_lang('ccx_template_sql_filter_key_invalid', 'Filter definition #%d has an invalid key "%s". Use letters, numbers or underscores.'), $position, $key);
                continue;
            }

            if (isset($seenKeys[$key])) {
                $errors[] = sprintf(ccx_lang('ccx_template_sql_filter_key_duplicate', 'Filter definition #%d reuses the key "%s". Keys must be unique.'), $position, $key);
                continue;
            }

            $label = isset($rawFilter['label']) ? trim((string) $rawFilter['label']) : '';
            if ($label === '') {
                $errors[] = sprintf(ccx_lang('ccx_template_sql_filter_label_required', 'Filter definition #%d requires a label.'), $position);
                continue;
            }

            $type = isset($rawFilter['type']) ? strtolower(trim((string) $rawFilter['type'])) : 'text';
            $allowedTypes = ['text', 'number', 'date', 'datetime', 'select'];
            if (! in_array($type, $allowedTypes, true)) {
                $errors[] = sprintf(ccx_lang('ccx_template_sql_filter_type_invalid', 'Filter definition #%d uses an unsupported type "%s".'), $position, $type);
                continue;
            }

            $filter = [
                'key'         => $key,
                'label'       => $label,
                'type'        => $type,
                'required'    => ! empty($rawFilter['required']),
                'placeholder' => isset($rawFilter['placeholder']) ? trim((string) $rawFilter['placeholder']) : '',
                'description' => isset($rawFilter['description']) ? trim((string) $rawFilter['description']) : '',
            ];

            if (array_key_exists('default', $rawFilter)) {
                $filter['default'] = $rawFilter['default'];
            }

            if ($type === 'number' && isset($filter['default']) && $filter['default'] !== '' && ! is_numeric($filter['default'])) {
                $errors[] = sprintf(ccx_lang('ccx_template_sql_filter_default_number', 'Filter definition #%d has a default value that is not numeric.'), $position);
                continue;
            }

            if ($type === 'select') {
                $optionsRaw = isset($rawFilter['options']) ? $rawFilter['options'] : [];
                if (! is_array($optionsRaw) || empty($optionsRaw)) {
                    $errors[] = sprintf(ccx_lang('ccx_template_sql_filter_options_required', 'Filter definition #%d requires at least one select option.'), $position);
                    continue;
                }

                $options = [];
                foreach ($optionsRaw as $optionIndex => $option) {
                    if (! is_array($option)) {
                        $errors[] = sprintf(ccx_lang('ccx_template_sql_filter_option_invalid', 'Filter definition #%d contains an invalid option at position #%d.'), $position, $optionIndex + 1);
                        continue 2;
                    }

                    $value = isset($option['value']) ? (string) $option['value'] : null;
                    $text  = isset($option['label']) ? (string) $option['label'] : null;

                    if ($value === null || $text === null || $value === '' || $text === '') {
                        $errors[] = sprintf(ccx_lang('ccx_template_sql_filter_option_invalid', 'Filter definition #%d contains an invalid option at position #%d.'), $position, $optionIndex + 1);
                        continue 2;
                    }

                    $options[] = [
                        'value' => $value,
                        'label' => $text,
                    ];
                }

                if (empty($options)) {
                    $errors[] = sprintf(ccx_lang('ccx_template_sql_filter_options_required', 'Filter definition #%d requires at least one select option.'), $position);
                    continue;
                }

                $filter['options'] = $options;
                if (isset($filter['default']) && $filter['default'] !== '') {
                    $filter['default'] = (string) $filter['default'];
                }
            }

            $seenKeys[$key] = true;
            $normalized[]   = $filter;
        }

        return [$normalized, $errors];
    }

    /**
     * @param string|null $filtersJson
     *
     * @return array<int,array<string,mixed>>
     */
    private function decode_saved_filters(?string $filtersJson): array
    {
        if ($filtersJson === null || trim($filtersJson) === '') {
            return [];
        }

        $decoded = json_decode((string) $filtersJson, true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * @param array<int,array<string,mixed>> $definitions
     *
     * @return array{
     *     filters: array<int,array<string,mixed>>,
     *     values: array<string,mixed>,
     *     has_errors: bool,
     *     submitted: bool
     * }
     */
    private function build_filter_context(array $definitions, ?array $input = null, ?bool $submittedOverride = null): array
    {
        if ($input === null) {
            $rawInput  = $this->input->get('filters');
            $submitted = $submittedOverride ?? isset($_GET['filters']);
        } else {
            $rawInput  = $input;
            $submitted = $submittedOverride ?? true;
        }

        if (! is_array($rawInput)) {
            $rawInput = [];
        }

        $filtersForView = [];
        $values         = [];
        $hasErrors      = false;

        foreach ($definitions as $definition) {
            if (! is_array($definition) || empty($definition['key'])) {
                continue;
            }

            $key         = $definition['key'];
            $type        = isset($definition['type']) ? strtolower((string) $definition['type']) : 'text';
            $required    = ! empty($definition['required']);
            $placeholder = isset($definition['placeholder']) ? (string) $definition['placeholder'] : '';
            $description = isset($definition['description']) ? (string) $definition['description'] : '';
            $options     = is_array($definition['options'] ?? null) ? $definition['options'] : [];
            $default     = isset($definition['default']) ? (string) $definition['default'] : '';

            $inputValue = $submitted ? ($rawInput[$key] ?? null) : $default;

            if (is_array($inputValue)) {
                $inputValue = reset($inputValue);
            }

            $inputValue = $inputValue === null ? '' : (string) $inputValue;
            $inputValue = trim($inputValue);

            $bindingValue = null;
            $error        = null;
            $valueForForm = $inputValue;

            switch ($type) {
                case 'number':
                    if ($inputValue === '') {
                        if ($required) {
                            $error = ccx_lang('ccx_template_sql_filter_error_required', 'This field is required.');
                        } else {
                            $bindingValue = null;
                        }
                    } elseif (! is_numeric($inputValue)) {
                        $error = ccx_lang('ccx_template_sql_filter_error_number', 'Enter a valid number.');
                    } else {
                        $bindingValue = strpos($inputValue, '.') !== false ? (float) $inputValue : (int) $inputValue;
                    }
                    break;

                case 'date':
                    if ($inputValue === '') {
                        if ($required) {
                            $error = ccx_lang('ccx_template_sql_filter_error_required', 'This field is required.');
                        } else {
                            $bindingValue = null;
                        }
                    } else {
                        $date = \DateTime::createFromFormat('Y-m-d', $inputValue);
                        if (! $date || $date->format('Y-m-d') !== $inputValue) {
                            $error = ccx_lang('ccx_template_sql_filter_error_date', 'Enter a valid date (YYYY-MM-DD).');
                        } else {
                            $bindingValue = $date->format('Y-m-d');
                        }
                    }
                    break;

                case 'datetime':
                    if ($inputValue === '') {
                        if ($required) {
                            $error = ccx_lang('ccx_template_sql_filter_error_required', 'This field is required.');
                        } else {
                            $bindingValue = null;
                        }
                    } else {
                        $date = \DateTime::createFromFormat('Y-m-d H:i:s', $inputValue)
                            ?: \DateTime::createFromFormat('Y-m-d\TH:i:s', $inputValue)
                            ?: \DateTime::createFromFormat('Y-m-d\TH:i', $inputValue)
                            ?: \DateTime::createFromFormat('Y-m-d H:i', $inputValue);

                        if (! $date) {
                            $error = ccx_lang('ccx_template_sql_filter_error_datetime', 'Enter a valid date & time.');
                        } else {
                            $bindingValue = $date->format('Y-m-d H:i:s');
                            $valueForForm = $date->format('Y-m-d\TH:i');
                        }
                    }
                    break;

                case 'select':
                    $allowed = [];
                    foreach ($options as $option) {
                        if (is_array($option) && isset($option['value'])) {
                            $allowed[(string) $option['value']] = $option['label'] ?? $option['value'];
                        }
                    }

                    if ($inputValue === '') {
                        if ($required) {
                            $error = ccx_lang('ccx_template_sql_filter_error_required', 'This field is required.');
                        } else {
                            $bindingValue = null;
                        }
                    } elseif (! array_key_exists($inputValue, $allowed)) {
                        $error = ccx_lang('ccx_template_sql_filter_error_choice', 'Select a valid option.');
                    } else {
                        $bindingValue = $inputValue;
                    }
                    break;

                case 'text':
                default:
                    if ($inputValue === '') {
                        if ($required) {
                            $error = ccx_lang('ccx_template_sql_filter_error_required', 'This field is required.');
                        } else {
                            $bindingValue = null;
                        }
                    } else {
                        $bindingValue = $inputValue;
                        if ($bindingValue !== '') {
                            if (function_exists('mb_substr')) {
                                $bindingValue = mb_substr($bindingValue, 0, 1000);
                            } else {
                                $bindingValue = substr($bindingValue, 0, 1000);
                            }
                        }
                    }
                    break;
            }

            if ($error !== null) {
                $hasErrors = true;
            }

            $filtersForView[] = [
                'key'         => $key,
                'label'       => $definition['label'],
                'type'        => $type,
                'required'    => $required,
                'placeholder' => $placeholder,
                'description' => $description,
                'options'     => $options,
                'value'       => $valueForForm,
                'error'       => $error,
            ];

            $values[$key] = $bindingValue;
        }

        return [
            'filters'   => $filtersForView,
            'values'    => $values,
            'has_errors'=> $hasErrors,
            'submitted' => $submitted,
        ];
    }
}
