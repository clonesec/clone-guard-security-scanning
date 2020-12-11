<?php
defined('ABSPATH') || exit;

class Clone_Guard_API {
    public $key = 'cgss';
    public $key_ = 'cgss_';

    public $api_key = '';
    public $user_token = '';

    // Base URL for API.
    public $base_url = 'https://pciscan.clone-systems.com/API/v1';

    // The class constructor.
    public function __construct() {
            $this->api_key = get_option($this->key_ . 'api_key');
            $this->user_token = get_option($this->key_ . 'user_token');
    }

    // Base method to make API calls.
    public function api($method, $url, $data = []) {
        $headers = [];
        //$headers[] = 'Authorization: Basic ' . $this->user_token;
        //$headers[] = 'x-api-key: ' . $this->api_key;
        $headers['Authorization'] = 'Basic ' . $this->user_token;
        $headers['x-api-key'] = $this->api_key;

        $args = [];
        $args['timeout'] = 10;
        $args['headers'] = $headers;
        if($method == 'DELETE') {
            $args['method'] = 'DELETE';
        } elseif($method == 'POST') {
            $args['method'] = 'POST';
            $args['body'] = $data;
        } elseif($method == 'PUT') {
            $args['method'] = 'PUT';
            $args['body'] = $data;
        } else {
            $args['method'] = 'GET';
        }
        $res = wp_remote_request($url, $args);

        $status_code = wp_remote_retrieve_response_code($res);
        $response = wp_remote_retrieve_body($res);

        if($status_code == 401) {
            return false;
        } elseif($status_code == 404) {
            return json_decode($response, true);
        } elseif($status_code == 422) {
            return json_decode($response, true);
        } else {
            return $response;
        }
    }

    // Creates a notification.
    public function createNotification($item) {
        $url = $this->base_url . '/notifications';

        $response = $this->api('POST', $url, $item);

        if($response === false) {
            return false;
        } elseif(isset($response['status_text'])) {
            return $response['status_text'];
        } else {
            return true;
        }
    }

    // Creates a scan.
    public function createScan($item) {
        $url = $this->base_url . '/scans';

        $response = $this->api('POST', $url, $item);

        if($response === false) {
            return false;
        } elseif(isset($response['status_text'])) {
            return $response['status_text'];
        } else {
            return true;
        }
    }

    // Creates a schedule.
    public function createSchedule($item) {
        $url = $this->base_url . '/schedules';

        $response = $this->api('POST', $url, $item);

        if($response === false) {
            return false;
        } elseif(isset($response['status_text'])) {
            return $response['status_text'];
        } else {
            return true;
        }
    }

    // Creates a target.
    public function createTarget($item) {
        $url = $this->base_url . '/targets';

        $response = $this->api('POST', $url, $item);

        if($response === false) {
            return false;
        } elseif(isset($response['status_text'])) {
            return $response['status_text'];
        } else {
            return true;
        }
    }

    // Deletes a report.
    public function deleteReport($id) {
        $url = $this->base_url . '/reports/' . $id;

        $response = $this->api('DELETE', $url);

        if($response === false) {
            return false;
        } else {
            return true;
        }
    }

    // Deletes a scan.
    public function deleteScan($id) {
        $url = $this->base_url . '/scans/' . $id;

        $response = $this->api('DELETE', $url);

        if($response === false) {
            return false;
        } else {
            return true;
        }
    }

    // Downloads a report.
    public function downloadReport($id, $type) {
        // URL ends with .pdf whether it is really a PDF or XLS file.
        $url = $this->base_url . '/reports/' . $id . '.pdf?type=' . $type;

        $response = $this->api('GET', $url);

        if(isset($response['status_text'])) {
            echo $response['status_text'];
        } else {
            // Remediation files are excel files.
            if($type == 'remediation') {
                header('Content-type:application/vnd.ms-excel');
                header('Content-Disposition: attachment; filename=' . $type . '.xls');
            } else {
                header('Content-type:application/pdf');
                header('Content-Disposition: attachment; filename=' . $type . '.pdf');
            }
            header('Pragma: no-cache');
            echo $response;
        }

        exit;
    }

    // Generates a report.
    public function generateReport($id, $type) {
        $url = $this->base_url . '/reports/' . $id . '/generate?type=' . $type;

        $response = $this->api('GET', $url);

        if($response === false) {
            return false;
        } else {
            return true;
        }
    }

    // Checks if a report is generated.
    public function generateReportCheck($id, $type) {
        // URL ends with .pdf whether it is really a PDF or XLS file.
        $url = $this->base_url . '/reports/' . $id . '/details';

        $response = $this->api('GET', $url);

        if(isset($response['status_text'])) {
            return false;
        } elseif(strpos($response, '{"status":404,') !== -1) {
            $data = json_decode($response, true);
            if(count($data['reports'])) {
                $reports = $data['reports'][0];
                foreach($reports as $key => $report) {
                    if(
                        isset($report['files']) 
                        && isset($report['files'][$type]) 
                        && isset($report['files'][$type]['status'])
                        && $report['files'][$type]['status'] == 'done'
                    ) {
                        return true;
                    } else {
                        return false;
                    }
                    break;
                }
            } else {
                return false;
            }
        }

        return false;
    }

    // Get all the notifications.
    public function getAllNotifications() {
        $output = [];

        $page = 1;
        $temp = $this->getNotifications($page);

        while(isset($temp['notifications']) && isset($temp['current_page']) && isset($temp['total_pages']) && $page <= $temp['total_pages']) {
            foreach($temp['notifications'] as $notification) {
                array_push($output, $notification);
            }

            $page++;
            $temp = $this->getNotifications($page);
        }

        $names = [];
        foreach($output as $notification) {
            $names[] = $notification['name'];
        }
        array_multisort($names, $output);

        return $output;
    }

    // Get all the schedules.
    public function getAllSchedules() {
        $output = [];

        $page = 1;
        $temp = $this->getSchedules($page);

        while(isset($temp['schedules']) && isset($temp['current_page']) && isset($temp['total_pages']) && $page <= $temp['total_pages']) {
            foreach($temp['schedules'] as $schedule) {
                array_push($output, $schedule);
            }

            $page++;
            $temp = $this->getSchedules($page);
        }

        $names = [];
        foreach($output as $schedule) {
            $names[] = $schedule['name'];
        }
        array_multisort($names, $output);

        return $output;
    }

    // Get all the targets.
    public function getAllTargets() {
        $output = [];

        $page = 1;
        $temp = $this->getTargets($page);

        while(isset($temp['targets']) && isset($temp['current_page']) && isset($temp['total_pages']) && $page <= $temp['total_pages']) {
            foreach($temp['targets'] as $target) {
                array_push($output, $target);
            }

            $page++;
            $temp = $this->getTargets($page);
        }

        $names = [];
        foreach($output as $target) {
            $names[] = $target['name'];
        }
        array_multisort($names, $output);

        return $output;
    }

    // Get a page of notifications.
    public function getNotifications($page = 1) {
        $output = [];
        $output['scans'] = [];
        $output['total'] = 0;
        $output['total_pages'] = 0;
        $output['current_page'] = 1;

        $url = $this->base_url . '/notifications?page=' . $page;

        $response = $this->api('GET', $url);

        if($response === false) {
            return false;
        }

        $data = json_decode($response, true);
        if(count($data['notifications'])) {
            $output['notifications'] = $data['notifications'][0];
            $output['total'] = $data['pagination']['total_count'];
            $output['total_pages'] = $data['pagination']['total_pages'];
            $output['current_page'] = $data['pagination']['current_page'];
            return $output;
        } else {
            return $output;
        }
    }

    // Get a report.
    public function getReport($id) {
        $output = [];

        $url = $this->base_url . '/reports/' . $id;
        $response = $this->api('GET', $url);

        $data = json_decode($response, true);
        if(count($data['reports'])) {
            $reports = $data['reports'][0];
            foreach($reports as $key => $report) {
                $output = $report;
                return $output;
                break;
            }
            return $output;
        } else {
            return $output;
        }
    }

    // Get a page of reports.
    public function getReports($page) {
        $output = [];
        $output['reports'] = [];
        $output['total'] = 0;
        $output['total_pages'] = 0;
        $output['current_page'] = 1;

        $url = $this->base_url . '/reports';

        $response = $this->api('GET', $url);

        if($response === false) {
            return false;
        }

        $data = json_decode($response, true);
        if(count($data['reports'])) {
            $output['reports'] = $data['reports'][0];
            $output['total'] = $data['pagination']['total_count'];
            $output['total_pages'] = $data['pagination']['total_pages'];
            $output['current_page'] = $data['pagination']['current_page'];
            return $output;
        } else {
            return $output;
        }
    }

    // Get a scan.
    public function getScan($id) {
        $output = [];

        $url = $this->base_url . '/scans/' . $id;
        $response = $this->api('GET', $url);

        $data = json_decode($response, true);
        if(count($data['scans'])) {
            $scans = $data['scans'][0];
            foreach($scans as $key => $scan) {
                $output = $scan;
                return $output;
                break;
            }
            return $output;
        } else {
            return $output;
        }
    }

    // Get a page of scans.
    public function getScans($page) {
        $output = [];
        $output['scans'] = [];
        $output['total'] = 0;
        $output['total_pages'] = 0;
        $output['current_page'] = 1;

        $url = $this->base_url . '/scans';

        $response = $this->api('GET', $url);

        if($response === false) {
            return false;
        }

        $data = json_decode($response, true);
        if(count($data['scans'])) {
            //echo '<pre>'; print_r($data['scans'][0]);die;
            $output['scans'] = $data['scans'][0];
            $output['total'] = $data['pagination']['total_count'];
            $output['total_pages'] = $data['pagination']['total_pages'];
            $output['current_page'] = $data['pagination']['current_page'];
            return $output;
        } else {
            return $output;
        }
    }

    // Get a schedule.
    public function getSchedule($id) {
        $output = [];

        $url = $this->base_url . '/schedules/' . $id;
        $response = $this->api('GET', $url);

        $data = json_decode($response, true);
        if(count($data['schedules'])) {
            $schedules = $data['schedules'][0];
            foreach($schedules as $key => $schedule) {
                $output = $schedule;
                return $output;
                break;
            }
            return $output;
        } else {
            return $output;
        }
    }

    // Get a page of schedules.
    public function getSchedules($page = 1) {
        $output = [];
        $output['scans'] = [];
        $output['total'] = 0;
        $output['total_pages'] = 0;
        $output['current_page'] = 1;

        $url = $this->base_url . '/schedules?page=' . $page;

        $response = $this->api('GET', $url);

        if($response === false) {
            return false;
        }

        $data = json_decode($response, true);
        if(count($data['schedules'])) {
            $output['schedules'] = $data['schedules'][0];
            $output['total'] = $data['pagination']['total_count'];
            $output['total_pages'] = $data['pagination']['total_pages'];
            $output['current_page'] = $data['pagination']['current_page'];
            return $output;
        } else {
            return $output;
        }
    }

    // Get a page of targets.
    public function getTargets($page = 1) {
        $output = [];
        $output['scans'] = [];
        $output['total'] = 0;
        $output['total_pages'] = 0;
        $output['current_page'] = 1;

        $url = $this->base_url . '/targets?page=' . $page;

        $response = $this->api('GET', $url);

        if($response === false) {
            return false;
        }

        $data = json_decode($response, true);
        if(count($data['targets'])) {
            $output['targets'] = $data['targets'][0];
            $output['total'] = $data['pagination']['total_count'];
            $output['total_pages'] = $data['pagination']['total_pages'];
            $output['current_page'] = $data['pagination']['current_page'];
            return $output;
        } else {
            return $output;
        }
    }

    // Start a scan.
    public function startScan($id) {
        $url = $this->base_url . '/scans/' . $id . '/start';

        $response = $this->api('POST', $url);

        if($response === false) {
            return false;
        } else {
            return true;
        }
    }

    // Start a scheduled scan.
    public function startScanScheduled($id) {
        $url = $this->base_url . '/scans/' . $id . '/start_scheduled';

        $response = $this->api('POST', $url);

        if($response === false) {
            return false;
        } else {
            return true;
        }
    }

    // Stop a scan.
    public function stopScan($id) {
        $url = $this->base_url . '/scans/' . $id . '/stop';

        $response = $this->api('POST', $url);

        if($response === false) {
            return false;
        } else {
            return true;
        }
    }

    // Update a scan.
    public function updateScan($id, $item) {
        $url = $this->base_url . '/scans/' . $id;

        $response = $this->api('PUT', $url, $item);

        if($response === false) {
            return false;
        } elseif(isset($response['status_text'])) {
            return $response['status_text'];
        } else {
            return true;
        }
    }

    // Update a schedule.
    public function updateSchedule($id, $item) {
        $url = $this->base_url . '/schedules/' . $id;

        $response = $this->api('PUT', $url, $item);

        if($response === false) {
            return false;
        } elseif(isset($response['status_text'])) {
            return $response['status_text'];
        } else {
            return true;
        }
    }

}

