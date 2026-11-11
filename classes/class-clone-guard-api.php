<?php
defined('ABSPATH') || exit;

class Clone_Guard_API {
    public $key = 'cgss';
    public $key_ = 'cgss_';

    public $portal_url = '';
    public $api_key = '';
    public $user_token = '';

    public $base_url = '';

    // Base URL for API.
    // public $base_url = 'https://pciscan.clone-systems.com/API/v1';

    // The class constructor.
    public function __construct() {
            $this->portal_url = get_option($this->key_ . 'portal_url');
            $this->api_key = get_option($this->key_ . 'api_key');
            $this->user_token = get_option($this->key_ . 'user_token');
            $this->base_url = 'https://' . $this->portal_url . '/API/v1';
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
    // TODO
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

    // Get all the schedules.
    public function getAllSchedules($page = 1) {
        $output = [];
        $output['schedules'] = [];
        $output['total'] = 0;
        $output['total_pages'] = 0;
        $output['current_page'] = $page;

        $url = $this->base_url . '/schedules';
        if (isset($page)) {
            $url = $url."?page=".$page;
        }

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

    // Get all the targets.
    public function getAllTargets($page = 1) {
        $output = [];
        $output['targets'] = [];
        $output['total'] = 0;
        $output['total_pages'] = 0;
        $output['current_page'] = $page;

        $url = $this->base_url . '/targets';
        if (isset($page)) {
            $url = $url."?page=".$page;
        }

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

    // Get all the notifications.
    public function getAllNotifications($page = 1) {
        $output = [];
        $output['notifications'] = [];
        $output['total'] = 0;
        $output['total_pages'] = 0;
        $output['current_page'] = $page;

        $url = $this->base_url . '/notifications';
        if (isset($page)) {
            $url = $url."?page=".$page;
        }

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

    // Get scanners.
    public function getScanners() {
        $output = [];
        $output['scanners'] = [];
        $output['total'] = 0;
        $output['total_pages'] = 0;
        $output['current_page'] = 1;

        $url = $this->base_url . '/scanners/activated'; //?per_page=20

        $response = $this->api('GET', $url);

        if($response === false) {
            return false;
        }

        $data = json_decode($response, true);
        // return $data;
        if(count($data['scanners'])) {
            $output['scanners'] =  $data['scanners'][0];
            $output['total'] = $data['count']['total'];
            // $output['total_pages'] = $data['pagination']['total_pages'];
            // $output['current_page'] = $data['pagination']['current_page'];
            return $output;
        } else {
            return $output;
        }
    }

    // Get scan configs.
    public function getScanConfigs() {
        $output = [];
        $output['configs'] = [];
        $output['total'] = 0;
        $output['total_pages'] = 0;
        $output['current_page'] = 1;

        $url = $this->base_url . '/configs?active=false'."&per_page=100";

        $response = $this->api('GET', $url);

        if($response === false) {
            return false;
        }

        $data = json_decode($response, true);
        // return $data;
        if(count($data['configs'])) {
            $output['configs'] =  $data['configs'][0];
            $output['total'] = $data['pagination']['total_count'];
            $output['total_pages'] = $data['pagination']['total_pages'];
            $output['current_page'] = $data['pagination']['current_page'];
            return $output;
        } else {
            return $output;
        }
    }

    // Get a notification.
    public function getNotification($id) {
        $output = [];

        $url = $this->base_url . '/notifications/' . $id;
        $response = $this->api('GET', $url);

        $data = json_decode($response, true);
        if(count($data['notifications'])) {
            $notifications = $data['notifications'][0];
            foreach($notifications as $key => $notification) {
                $output = $notification;
                return $output;
                break;
            }
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
    public function getReports($page = 1, $perPage = 10) {
        $output = [];
        $output['reports'] = [];
        $output['total'] = 0;
        $output['total_pages'] = 0;
        $output['current_page'] = $page;

        $url = $this->base_url . '/reports';
        if (isset($page) && !isset($perPage)) {
            $url = $url."?page=".$page;
        } elseif (isset($page) && isset($perPage)) {
            $url = $url."?page=".$page."&per_page=".$perPage;
        }

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

    // Get results - vulnerabilities.
    public function getResults($page = 1, $perPage = 10) {
        $output = [];
        $output['results'] = [];
        $output['total'] = 0;
        $output['total_pages'] = 0;
        $output['current_page'] = $page;

        $url = $this->base_url . '/results';
        if (isset($page) && !isset($perPage)) {
            $url = $url."?page=".$page;
        } elseif (isset($page) && isset($perPage)) {
            $url = $url."?page=".$page."&per_page=".$perPage;
        }

        $response = $this->api('GET', $url);

        if($response === false) {
            return false;
        }

        $data = json_decode($response, true);
        // return $data;
        if(count($data['results'])) {
            $output['results'] = $data['results'];
            $output['total'] = $data['pagination']['total_count'];
            $output['total_pages'] = $data['pagination']['total_pages'];
            $output['current_page'] = $data['pagination']['current_page'];          
            return $output;
        } else {
            return $output;
        }
    }

    // Get a result.
    public function getResult($id) {
        $output = [];

        $url = $this->base_url . '/results/' . $id;
        $response = $this->api('GET', $url);

        $data = json_decode($response, true);
        if(count($data['results'])) {
            $results = $data['results'];
            foreach($results as $key => $result) {
                $output = $result;
                return $output;
                break;
            }
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
    public function getScans($page = 1) {
        $output = [];
        $output['scans'] = [];
        $output['total'] = 0;
        $output['total_pages'] = 0;
        $output['current_page'] = $page;

        $url = $this->base_url . '/scans';
        if (isset($page)) {
            $url = $url."?page=".$page;
        }

        $response = $this->api('GET', $url);

        if($response === false) {
            return false;
        }

        $data = json_decode($response, true);
        if(count($data['scans'])) {
            $output['scans'] = $data['scans'][0];
            $output['total'] = $data['pagination']['total_count'];
            $output['total_pages'] = $data['pagination']['total_pages'];
            $output['current_page'] = $data['pagination']['current_page'];
            // echo '<pre>'; print_r($output); echo '</pre>';
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

    // Get a target.
    public function getTarget($id) {
        $output = [];

        $url = $this->base_url . '/targets/' . $id;
        $response = $this->api('GET', $url);

        $data = json_decode($response, true);
        if(count($data['targets'])) {
            $targets = $data['targets'][0];
            foreach($targets as $key => $target) {
                $output = $target;
                return $output;
                break;
            }
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

    // Update a target.
    public function updateTarget($id, $item) {
        $url = $this->base_url . '/targets/' . $id;

        $response = $this->api('PUT', $url, $item);

        if($response === false) {
            return false;
        } elseif(isset($response['status_text'])) {
            return $response['status_text'];
        } else {
            return true;
        }
    }

    // Update a notification.
    public function updateNotification($id, $item) {
        $url = $this->base_url . '/notifications/' . $id;

        $response = $this->api('PUT', $url, $item);

        if($response === false) {
            return false;
        } elseif(isset($response['status_text'])) {
            return $response['status_text'];
        } else {
            return true;
        }
    }

    // Deletes a schedule.
    public function deleteSchedule($id) {
        $url = $this->base_url . '/schedules/' . $id;

        $response = $this->api('DELETE', $url);

        if($response === false) {
            return false;
        } else {
            return true;
        }
    }

    // Deletes a target.
    public function deleteTarget($id) {
        $url = $this->base_url . '/targets/' . $id;

        $response = $this->api('DELETE', $url);

        if($response === false) {
            return false;
        } else {
            return true;
        }
    }

    // Deletes a notification.
    public function deleteNotification($id) {
        $url = $this->base_url . '/notifications/' . $id;

        $response = $this->api('DELETE', $url);

        if($response === false) {
            return false;
        } else {
            return true;
        }
    }

    // Get the user details.
    public function getUserDetails() {
        $url = $this->base_url . '/my_details';

        $response = $this->api('GET', $url);

        if($response === false) {
            return false;
        }

        $data = json_decode($response, true);
        if (isset($data)) {
            return $data['details'];
        }
    }

    // Change user's app type.
    public function updateUserAppType($appType) {
        $url = $this->base_url . '/modules/?app_type=' . $appType;

        $response = $this->api('PUT', $url);

        if($response === false) {
            return false;
        } elseif(isset($response['status_text'])) {
            return $response['status_text'];
        } else {
            return true;
        }

    }

    public function getException($id) {
        $output = [];

        $url = $this->base_url . '/exceptions/' . $id;
        $response = $this->api('GET', $url);

        $data = json_decode($response, true);
        
        return $data['exceptions'];
        
    }

    // Creates an exception.
    public function createException($item) {
        $url = $this->base_url . '/exceptions';

        $response = $this->api('POST', $url, $item);
    
        if($response === false) {
            return false;
        } elseif(isset($response['status_text'])) {
            return $response['status_text'];
        } else {
            return true;
        }
    }

    // Updates an exception.
    public function updateException($id, $text, $nvt_oid) {
        // $url = $this->base_url . '/exceptions/' . $id;

        $url = $this->base_url . '/exceptions/' . $id . '?exceptions[text]=' . $text . '&exceptions[nvt]=' . $nvt_oid;

        $response = $this->api('PUT', $url);

        if($response === false) {
            return false;
        } elseif(isset($response['status_text'])) {
            return $response['status_text'];
        } else {
            return true;
        }
    }

}

