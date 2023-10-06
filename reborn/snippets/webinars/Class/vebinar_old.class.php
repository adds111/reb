<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/assets/snippets/lib/lib.php';

class Vebinar extends Fetch
{
    protected function registration($data)
    {
        if (!is_array($data['ids']) || empty($data['ids'])) {
            json(array('result' => false));
            return false;
        }

        $doubles = $success = array();

        foreach ($data['ids'] as $id) {
            $data['vebinar_id'] = $id;

            if ($this->check_existence('vebinar_watchers_new', get_some_array_fields($data, array('email', 'vebinar_id')))) {
                $doubles[] = $id;
                continue;
            }

            $result = parent::insert('vebinar_watchers_new', $data);

            if ($result) {
                $success[] = $id;
            }
        }

        json(array('result' => $result, 'doubles' => $doubles, 'success' => $success));
        return true;
    }

    private function save_new_company($data)
    {
        $companyName = str_replace('"', '\"', trim($data['company']));
        $email = trim($data['email']);
        $phone = preg_replace('/[^\d\+]/', '', trim($data['phone']));
        
        new Webinar(array()); // new Vebinar(array('db-config' => $GLOBALS['revo']));
        $sql = <<<SQL
SELECT *
FROM `inn`
WHERE `companyName` = "$companyName"
SQL;
        $company = database::getInstance()->Select($sql);

        if (empty($company)) {
            return false;
        }

        $id = $company[0]['id'];

        $sql = <<<SQL
UPDATE `inn`
SET 
`email` = CONCAT(`email`, "\n", "$email"),
`phone` = CONCAT(`phone`, "\n", "$phone")
WHERE `id` = $id
SQL;
        database::getInstance()->Query($sql);

        return true;
    }

    protected function registration_one($data)
    {
        if (!intval($data['vebinar_id'])) {
            json(array('result' => false));
            return false;
            
        } elseif ($this->check_existence('vebinar_watchers_new', get_some_array_fields($data, array('email', 'vebinar_id')))) {
            json(array('result' => false, 'note' => 'Вы уже записаны на данный вебинар'));
            return true;
        }

        $data['sms_reminder'] = isset($data['sms_reminder']) ? 1 : 0;
        $data['whatsApp_reminder'] = isset($data['whatsApp_reminder']) ? 1 : 0;
        $data['roistat_id'] = $_COOKIE['roistat_param_company'];

        $vebinarDate = $this->getter('modx_site_tmplvar_contentvalues', array('tmplvarid' => 140, 'contentid' => $data['vebinar_id']), 'value');
        $data['vebinar_date'] = $vebinarDate[0]['value'];

        $result = parent::insert('vebinar_watchers_new', $data);
        json(array('result' => $result, 'save_new_email' => $this->save_new_company($data)));
        return true;
    }


    protected function registration_one_test($data)
    {
        $multi_data = array();
        $ids = $data['ids'];
        unset($data['ids'], $data['method']);

        if (!is_array($ids)) {
            json(array('result' => 0));
            return false;
        }

        foreach ($data as $i => $item)
            $data[$i] = str_replace('"', '', $item);

        foreach ($ids as $id) {
            if ($this->check_existence('vebinar_watchers_new', array('email' => $data['email'], 'vebinar_id' => $id)))
                continue;
            $data['vebinar_id'] = $id;

            $vebinarDate = $this->getter('modx_site_tmplvar_contentvalues', array('tmplvarid' => 140, 'contentid' => $id), 'value');
            $data['vebinar_date'] = $vebinarDate[0]['value'];

            $multi_data[] = $data;
        }

        if (!empty($multi_data))
            $this->insert('vebinar_watchers_new', $multi_data, 1);

        $sql = 'SELECT `vebinar_id` 
                FROM `vebinar_watchers_new`
                WHERE 
                    `email` = "' . $data['email'] . '" AND
                    `vebinar_date` > NOW()
                 ORDER BY `vebinar_date`
                 LIMIT 1';
        $fetch = database::getInstance()->Select($sql);
        $first_vebinar_id = $fetch[0]['vebinar_id'];
        json(array(
                'result' => 1,
                'data' => $data,
                'calendar' => $this->get_vebinar_details(array('id' => $first_vebinar_id)),
                'save_new_email' => $this->save_new_company($data)
        ));
        return true;
    }

    protected function upload_vebinar_img_by_url($data)
    {
        $url = strval($data['url']);
        $path = $_SERVER['DOCUMENT_ROOT'] . '/assets/snippets/vebinar-manager/img/uploaded/' . time() . rand(0, 10000) . time() . '.jpg';
        json(array('file' => upload_img_by_url($url, $path)));
    }

    protected function delete_img($data)
    {
        $file = $_SERVER['DOCUMENT_ROOT'] . strval($data['file']);
        if (!file_exists($file)) {
            json(array('result' => false, 'message' => 'Файл не найден'));
            return false;
        }
        unlink($file);
        json(array('result' => !file_exists($file), 'message' => 'Файл ' . $file . ' удален'));
        return true;
    }

    static function upload_files($files)
    {
        $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/assets/snippets/vebinar-manager/img/uploaded/';
        $uploaded = upload_files($files, $upload_dir);
        json(array('file' => $uploaded[0]));
    }

    protected function add($data)
    {
        json(array('result' => parent::insert('vebinars', $data)));
    }

    protected function edit($data)
    {
        $id = intval($data['id']);
        $sql = 'SELECT 
                    `c`.`id` as `id`, 
                    IF(`c`.`alias` = "", `c`.`id`, `c`.`alias`) as `uri`, 
                    IFNULL(`v4`.`value`, `c`.`pagetitle`) as `title`, 
                    IFNULL(`v5`.`value`, `c`.`description`) as `description`,
                    `c`.`content` as `content`,                    
                    `v1`.`value` as `date`,
                    `v2`.`value` as `image`,
                    `v3`.`value` as `video`,
                    COUNT(`w`.`id`) as `watchers`
                 FROM `modx_site_content` as `c`
                    LEFT JOIN `modx_site_tmplvar_contentvalues` as `v1`
                        ON `c`.`id` = `v1`.`contentid` AND `v1`.`tmplvarid` = 140
                    LEFT JOIN `modx_site_tmplvar_contentvalues` as `v2`
                        ON `c`.`id` = `v2`.`contentid` AND `v2`.`tmplvarid` = 141
                    LEFT JOIN `modx_site_tmplvar_contentvalues` as `v3`
                        ON `c`.`id` = `v3`.`contentid` AND `v3`.`tmplvarid` = 142 
                    LEFT JOIN `modx_site_tmplvar_contentvalues` as `v4`
                        ON `c`.`id` = `v4`.`contentid` AND `v4`.`tmplvarid` = 143   
                    LEFT JOIN `modx_site_tmplvar_contentvalues` as `v5`
                        ON `c`.`id` = `v5`.`contentid` AND `v5`.`tmplvarid` = 144  
                    LEFT JOIN `vebinar_watchers_new` as `w`   
                        ON `c`.`id` = `w`.`vebinar_id`                 
                WHERE 
                    `c`.`id` = ' . $id . ' 
                LIMIT 1';
        $fetch = database::getInstance()->Select($sql);
        if (empty($fetch)) {
            json(array('result' => 0));
            return false;
        }

        $fetch[0]['editor'] = get_ob_content($_SERVER['DOCUMENT_ROOT'] . '/assets/snippets/vebinar-manager/html/html-editor-menu.php');

        $html = get_ob_content($_SERVER['DOCUMENT_ROOT'] . '/assets/snippets/vebinar-manager/html/edit-vebinar.php', $fetch[0]);

        json(array('result' => 1, 'html' => $html, 'data' => $fetch[0]));
        return false;
    }

    protected function update_status($data)
    {
        $update = parent::update('vebinars', $data, $data['id']);
        if (!$update) {
            json(array('result' => 0));
            return false;
        }

        $fetch = database::getInstance()->Select('SELECT `status` FROM `vebinars` WHERE `id` = ' . $data['id'] . ' LIMIT 1');
        json(array('result' => 1, 'status' => $fetch[0]['status']));
        return true;
    }

    protected function save($data)
    {
        $result = parent::update('vebinars', $data, $data['id']);
        if (is_array($result)) {
            $data = $result[0];
            $data['date'] = date_rus_format($data['date'], 1);
            $data['title'] = strip_tags($data['title']);
        }
        json(array('result' => $result ? true : false, 'data' => $data));
    }

    protected function init()
    {
    }

    public function get($id, $passed = false)
    {
        $sql = 'SELECT 
                    `c`.`id` as `id`, 
                    `c`.`published` as `published`, 
                    IF(`c`.`alias` = "", `c`.`id`, `c`.`alias`) as `uri`, 
                    IFNULL(`v4`.`value`, `c`.`pagetitle`) as `title`, 
                    IFNULL(`v5`.`value`, `c`.`description`) as `description`,
                    `c`.`content` as `content`,
                    `c`.`published` as `status`,
                    `v1`.`value` as `date`,
                    `v2`.`value` as `image`,
                    `v3`.`value` as `video`,
                    COUNT(`w`.`id`) as `watchers`
                 FROM `modx_site_content` as `c`
                    LEFT JOIN `modx_site_tmplvar_contentvalues` as `v1`
                        ON `c`.`id` = `v1`.`contentid` AND `v1`.`tmplvarid` = 140
                    LEFT JOIN `modx_site_tmplvar_contentvalues` as `v2`
                        ON `c`.`id` = `v2`.`contentid` AND `v2`.`tmplvarid` = 141
                    LEFT JOIN `modx_site_tmplvar_contentvalues` as `v3`
                        ON `c`.`id` = `v3`.`contentid` AND `v3`.`tmplvarid` = 142 
                    LEFT JOIN `modx_site_tmplvar_contentvalues` as `v4`
                        ON `c`.`id` = `v4`.`contentid` AND `v4`.`tmplvarid` = 143   
                    LEFT JOIN `modx_site_tmplvar_contentvalues` as `v5`
                        ON `c`.`id` = `v5`.`contentid` AND `v5`.`tmplvarid` = 144  
                    LEFT JOIN `vebinar_watchers_new` as `w`   
                        ON `c`.`id` = `w`.`vebinar_id`                 
                WHERE 
                    `c`.`parent` = ' . $id . ' AND
                    `v1`.`value` ' . ($passed ? '<' : '>') . ' CURRENT_TIMESTAMP
                GROUP BY `c`.`id`
                ORDER BY `v1`.`value`';

        return database::getInstance()->Select($sql);
    }

    public function get_all($id)
    {

        if (!intval($id))
            return false;

        $sql = 'SELECT 
                    `c`.`id` as `id`, 
                    IF(`c`.`alias` = "", `c`.`id`, `c`.`alias`) as `uri`, 
                    IFNULL(`v4`.`value`, `c`.`pagetitle`) as `title`, 
                    IFNULL(`v5`.`value`, `c`.`description`) as `description`,
                    `c`.`content` as `content`,
                    `v1`.`value` as `date`,
                    `v2`.`value` as `image`,
                    `v3`.`value` as `video`,
                    `v6`.`value` as `tag`,
                     IF(COUNT(`w`.`id`) < 10, FLOOR(RAND()*(30 - 10)), COUNT(`w`.`id`)) as `watchers`
                 FROM `modx_site_content` as `c`
                    LEFT JOIN `modx_site_tmplvar_contentvalues` as `v1`
                        ON `c`.`id` = `v1`.`contentid` AND `v1`.`tmplvarid` = 140
                    LEFT JOIN `modx_site_tmplvar_contentvalues` as `v2`
                        ON `c`.`id` = `v2`.`contentid` AND `v2`.`tmplvarid` = 141
                    LEFT JOIN `modx_site_tmplvar_contentvalues` as `v3`
                        ON `c`.`id` = `v3`.`contentid` AND `v3`.`tmplvarid` = 142 
                    LEFT JOIN `modx_site_tmplvar_contentvalues` as `v4`
                        ON `c`.`id` = `v4`.`contentid` AND `v4`.`tmplvarid` = 143   
                    LEFT JOIN `modx_site_tmplvar_contentvalues` as `v5`
                        ON `c`.`id` = `v5`.`contentid` AND `v5`.`tmplvarid` = 144  
                    LEFT JOIN `vebinar_watchers_new` as `w`   
                        ON `c`.`id` = `w`.`vebinar_id`
                    LEFT JOIN `modx_site_tmplvar_contentvalues` as `v6`   
                        ON `c`.`id` = `v6`.`contentid` AND `v6`.`tmplvarid` = 146                       
                WHERE 
                    `c`.`parent` = ' . $id . ' AND 
                    `c`.`published` = 1
                GROUP BY `c`.`id`
                ORDER BY `v1`.`value` DESC';

        return database::getInstance()->Select($sql);
    }


    public function get_watchers($data)
    {
        if (!intval($data['vebinar_id'])) {
            json(array('result' => 0));
            return false;
        }

        $fetch = $this->getter(
            'vebinar_watchers_new',
            array('vebinar_id' => $data['vebinar_id']),
            array('id', 'name', 'company', 'position', 'email', 'phone', 'sms_reminder', 'whatsApp_reminder', 'roistat_id')
        );

        $detaitls = $this->get_vebinar_details(array('id' => $data['vebinar_id']));
        $date = $detaitls['date'];
        $title = $detaitls['title'];
        $date = current(explode(' ', $date));

        if ($data['export']) {
            $fetch = delete_some_fetch_fields($fetch);
            csv_generate($fetch, array('Имя', 'Название компании', 'Должность', 'Электронная почта', 'Телефон', 'Оповестить по смс', 'Оповестить по WhatsApp', 'Id для Ройстат'), 'vebinar-' . $date . '-watchers-list');
            return true;
        }

        $html = '';
        foreach ($fetch as $i => $row) {
            $row['number'] = $i + 1;
            $html .= get_ob_content($_SERVER['DOCUMENT_ROOT'] . '/assets/snippets/vebinar-manager/html/get-wathcers.php', $row);
        }
        json(array('result' => !empty($fetch), 'data' => $fetch, 'html' => $html, 'date' => $date, 'title' => $title));
        return true;
    }


    public function get_vebinar_details($data)
    {
        $id = intval($data['id']);
        if (!$id) {
            json(array('result' => 0, 'alert' => 'Не задан идентификатор'));
            return false;
        }

        //$sql = 'SELECT * FROM `vebinars` WHERE `status` = 1 AND `id` = ' . $id . ' LIMIT 1';

        $sql = 'SELECT 
                    `c`.`id` as `id`, 
                    IFNULL(`v4`.`value`, `c`.`pagetitle`) as `title`, 
                    IFNULL(`v5`.`value`, `c`.`description`) as `description`,
                    `c`.`content` as `content`,
                    `v1`.`value` as `date`,
                    `v2`.`value` as `image`,
                    `v3`.`value` as `video`,
                    COUNT(`w`.`id`) as `watchers`
                 FROM `modx_site_content` as `c`
                    LEFT JOIN `modx_site_tmplvar_contentvalues` as `v1`
                        ON `c`.`id` = `v1`.`contentid` AND `v1`.`tmplvarid` = 140
                    LEFT JOIN `modx_site_tmplvar_contentvalues` as `v2`
                        ON `c`.`id` = `v2`.`contentid` AND `v2`.`tmplvarid` = 141
                    LEFT JOIN `modx_site_tmplvar_contentvalues` as `v3`
                        ON `c`.`id` = `v3`.`contentid` AND `v3`.`tmplvarid` = 142
                    LEFT JOIN `modx_site_tmplvar_contentvalues` as `v4`
                        ON `c`.`id` = `v4`.`contentid` AND `v4`.`tmplvarid` = 143   
                    LEFT JOIN `modx_site_tmplvar_contentvalues` as `v5`
                        ON `c`.`id` = `v5`.`contentid` AND `v5`.`tmplvarid` = 144 
                    LEFT JOIN `vebinar_watchers_new` as `w`   
                        ON `c`.`id` = `w`.`vebinar_id`                         
                WHERE 
                    `c`.`id` = ' . $id . '
                GROUP BY `c`.`id`
                LIMIT 1';

        $fetch = database::getInstance()->Select($sql);
        $result = !empty($fetch);

        $fetch[0]['status'] = strtotime($fetch[0]['date']) > time();

        /**
         *    Добавление зрителей к вебинару
         */
        $fetch[0]['watchers'] += 10;

        if ($data['method'] == 'get_vebinar_details')
            json(array('result' => $result, 'data' => $fetch[0]));
        return $fetch[0];
    }


    protected function mailganer($data)
    {
        $api_key = '4e255e71d5efaf49ccd65f8d55b709a9';
        $watchers = $this->getter('vebinar_watchers_new', array('vebinar_id' => $data['vebinar_id']));

        $vebinar = $this->get_vebinar_details(array('id' => $data['vebinar_id']));
        $title = $vebinar['title'];
        $date = date('d.m.Y', strtotime($vebinar['date']));
        $date_time = date_rus_format($vebinar['date'], 1);
        $letter_file = $_SERVER['DOCUMENT_ROOT'] . '/assets/snippets/vebinar-manager/html/letter-html.php';
        $letter = get_ob_content($letter_file, array('title' => $title, 'date' => $date, 'date_time' => $date_time, 'link' => $data['link']));


        $source_id = false;

        foreach ($watchers as $i => $row) {
            if ($row['mailganer_source_id']) {
                $source_id = $row['mailganer_source_id'];
                unset($watchers[$i]);
            }
        }

        if (empty($watchers)) {
            json(array(
                'export' => 0,
                'result' => 'Все данные уже добавлены!'
            ));
            return false;
        }


        if (!$source_id) {
            //Создаем новый список
            $url = 'https://mailganer.com/api/sites/';
            $listName = 'watchers-list-' . time();
            $query = array(
                "api_key" => $api_key,
                "domain" => $listName
            );
            $addNewListResult = json_decode(curl_query($url, $query), 1);
            $source_id = intval($addNewListResult['id']);
        }


        if (!intval($source_id)) {
            json(array(
                'export' => 0,
                'result' => 'Ошибка! Новый список не создан...'
            ));
            return false;
        }

        //Добавляем в новый список email'ы
        $url = 'https://mailganer.com/api/email/add/';
        $result = array();
        $update = array();
        foreach ($watchers as $row) {
            $query = array(
                "api_key" => $api_key,
                "email" => $row['email'],
                "name" => $row['name'],
                "phone" => $row['phone'],
                "source_id" => $source_id,
                "not_doi" => 1
            );
            $response = json_decode(curl_query($url, $query), 1);
            if ($response['status'])
                $update[] = $this->update('vebinar_watchers_new', array('mailganer_source_id' => $source_id), $row['id']);
            $result[$row['email']] = $response;
        }

        //Создаем расылку
        $url = 'https://mailganer.com/api/mailing/create/';
        $query = array(
            "api_key" => $api_key,
            "theme" => $title,
            "html-content" => $letter
        );
        $response_mailing_create = json_decode(curl_query($url, $query), 1);

        json(array(
            'export' => 1,
            'result' => $result,
            'update' => $update,
            'listName' => $listName,
            'source_id' => $source_id,
            'newListInfo' => $addNewListResult,
            'mailing_create' => $response_mailing_create,
            'mailing_query' => $query
        ));
        return true;
    }


    protected function unisender($data)
    {
        $api_key = '59dns8ijjy59t9efdrw3418z76ts9gwrmi4igh9e';

        //Создание нового списка
        $listName = 'watchers-list-' . time();
        $url = 'https://api.unisender.com/ru/api/createList?format=json&api_key=' . $api_key . '&title=' . $listName;
        $addNewListResult = json_decode(curl_query($url), 1);

        if (!$addNewListResult['result']['id']) {
            json(array('result' => 0, 'message' => 'новый список не создан!!!'));
            return 0;
        }

        //Добавление в новый список подписчиков
        $list_id = (int)$addNewListResult['result']['id'];
        // $watchers = $this->getter('vebinar_watchers_new', array('vebinar_id' => $data['vebinar_id']));
        $watchers = array(
            array('phone' => '8-909-906-98-88', 'email' => 'titov_yw@mail.ru', 'name' => 'Юрий Титов'),
            array('phone' => '+79773781130', 'email' => 'avp@fluid-line.ru', 'name' => 'Пичугин Александр Викторович'),
            array('phone' => '+79265177261', 'email' => 'alex@fluid-line.ru', 'name' => 'Абдурашитов Алексей Маджитович'),
        );

        $result = array();
        foreach ($watchers as $row) {
            $url = 'https://api.unisender.com/ru/api/subscribe
                        ?format=json`
                        &api_key=' . $api_key . '
                        &list_ids=' . $list_id . '
                        &fields[email]=' . trim($row['email']) . '
                        &double_optin=3';
            $url = preg_replace("/(\n|\s)/", '', $url);
            $result[] = json_decode(curl_query($url), 1);
        }

        json(array(
            'added-watchers' => $result,
            'list-id' => $list_id
        ));
        return 1;
    }

    protected function clearMailganer($data)
    {
        $vebinar_id = intval($data['vebinar_id']);
        if (!$vebinar_id) {
            json(array('result' => 0, 'note' => 'Не передан id'));
            return false;
        }
        $result = $this->update(
            'vebinar_watchers_new',
            array('mailganer_source_id' => 0),
            $vebinar_id,
            'vebinar_id');

        json(array('result' => $result));

        return 1;
    }

    protected function deleteWatcher($data)
    {
        $id = intval($data['id']);
        if (!$id) {
            json(array('result' => 0, 'note' => 'Не передан id'));
            return false;
        }
        $delete = $this->delete('vebinar_watchers_new', $id);
        if ($delete)
            $result = !$this->check_existence('vebinar_watchers_new', array('id' => $id));
        json(array('result' => $delete ? $result : 0));
        return 1;
    }


    public function vebinar_filter($data)
    {
        $id = intval($data['id']);

        if ($data['tag'] == 'default') {
            $sql = 'SELECT 
                    `c`.`id` as `id`, 
                    IF(`c`.`alias` = "", `c`.`id`, `c`.`alias`) as `uri`, 
                    IFNULL(`v4`.`value`, `c`.`pagetitle`) as `title`, 
                    IFNULL(`v5`.`value`, `c`.`description`) as `description`,
                    `c`.`content` as `content`,
                    `v1`.`value` as `date`,
                    `v2`.`value` as `image`,
                    `v3`.`value` as `video`,
                    COUNT(`w`.`id`) as `watchers`
                 FROM `modx_site_content` as `c`
                    LEFT JOIN `modx_site_tmplvar_contentvalues` as `v1`
                        ON `c`.`id` = `v1`.`contentid` AND `v1`.`tmplvarid` = 140
                    LEFT JOIN `modx_site_tmplvar_contentvalues` as `v2`
                        ON `c`.`id` = `v2`.`contentid` AND `v2`.`tmplvarid` = 141
                    LEFT JOIN `modx_site_tmplvar_contentvalues` as `v3`
                        ON `c`.`id` = `v3`.`contentid` AND `v3`.`tmplvarid` = 142 
                    LEFT JOIN `modx_site_tmplvar_contentvalues` as `v4`
                        ON `c`.`id` = `v4`.`contentid` AND `v4`.`tmplvarid` = 143   
                    LEFT JOIN `modx_site_tmplvar_contentvalues` as `v5`
                        ON `c`.`id` = `v5`.`contentid` AND `v5`.`tmplvarid` = 144  
                    LEFT JOIN `vebinar_watchers_new` as `w`   
                        ON `c`.`id` = `w`.`vebinar_id`                     
                WHERE 
                    `c`.`parent` = ' . $id . ' AND 
                    `c`.`published` = 1 AND
                    `v1`.`value` < NOW()
                GROUP BY `c`.`id`
                ORDER BY `v1`.`value` DESC';
        } else {
            $sql = 'SELECT 
                    `c`.`id` as `id`, 
                    IF(`c`.`alias` = "", `c`.`id`, `c`.`alias`) as `uri`, 
                    IFNULL(`v4`.`value`, `c`.`pagetitle`) as `title`, 
                    IFNULL(`v5`.`value`, `c`.`description`) as `description`,
                    `c`.`content` as `content`,
                    `v1`.`value` as `date`,
                    `v2`.`value` as `image`,
                    `v3`.`value` as `video`,
                    `v6`.`value` as `tag`,
                    COUNT(`w`.`id`) as `watchers`
                 FROM `modx_site_content` as `c`
                    LEFT JOIN `modx_site_tmplvar_contentvalues` as `v1`
                        ON `c`.`id` = `v1`.`contentid` AND `v1`.`tmplvarid` = 140
                    LEFT JOIN `modx_site_tmplvar_contentvalues` as `v2`
                        ON `c`.`id` = `v2`.`contentid` AND `v2`.`tmplvarid` = 141
                    LEFT JOIN `modx_site_tmplvar_contentvalues` as `v3`
                        ON `c`.`id` = `v3`.`contentid` AND `v3`.`tmplvarid` = 142 
                    LEFT JOIN `modx_site_tmplvar_contentvalues` as `v4`
                        ON `c`.`id` = `v4`.`contentid` AND `v4`.`tmplvarid` = 143   
                    LEFT JOIN `modx_site_tmplvar_contentvalues` as `v5`
                        ON `c`.`id` = `v5`.`contentid` AND `v5`.`tmplvarid` = 144  
                    LEFT JOIN `vebinar_watchers_new` as `w`   
                        ON `c`.`id` = `w`.`vebinar_id`  
                    LEFT JOIN `modx_site_tmplvar_contentvalues` as `v6`   
                        ON `c`.`id` = `v6`.`contentid` AND `v6`.`tmplvarid` = 146                  
                WHERE 
                    `c`.`parent` = ' . $id . ' AND 
                    `c`.`published` = 1 AND
                    `v6`.`value` = "' . $data['tag'] . '" AND
                    `v1`.`value` < NOW()
                GROUP BY `c`.`id`
                ORDER BY `v1`.`value` DESC';
        }

        $fetch = database::getInstance()->Select($sql);
        
        /**
         *    Добавление зрителей к вебинару
         */
        $fetch[0]['watchers'] += 10;

        $html = '';

        foreach ($fetch as $i => $row) {
            $date = date_rus_format($row['date'], 1);
            $title = strip_tags($row['title']);
            $is_hidden = $i < 6 ? '' : 'hidden';
            $is_real_hidden = $i < 6 ? 'block' : 'none' ;
            $html .= <<<HTML
                <div class="vebinar-grid-item passed $is_hidden" style="display:$is_real_hidden;" data-id="$row[id]" onclick="window.location.href='$row[uri]'">
                    <p class="vebinar-date">$date</p>
                    <img src="$row[image]" alt="" class="vebinar-image"/>
                    <h3 class="vebinar-title">$title</h3>
                    <a href="$row[uri]">
                        <button class="select-vebinar" data-id="$row[id]">Смотреть</button>
                    </a>
                    <span class="vebinar-watchers" title="Количество человек, записавшихся на вебинар">
                        <i class="fa fa-eye" aria-hidden="true"></i>
                         $row[watchers]
                    </span>
                </div>
HTML;
        }
        json(array('result' => !empty($fetch), 'data' => $fetch, 'html' => $html, 'sql' => $sql));
        return true;
    }


    protected function get_whatsapp_phone_list($data)
    {
        $vebinar_id = intval($data['vebinar_id']);

        if (!$vebinar_id) {
            json(array('result' => 0, 'note' => 'Не передан id', 'data' => $data));
            return false;
        }

        $watchers = $this->getter('vebinar_watchers_new', array('vebinar_id' => $vebinar_id, 'whatsApp_reminder' => 1), 'phone');
        $watchers_phones = fetch_to_array($watchers, 'phone');

        $watchers_phones = array_unique($watchers_phones);
        json(array('result' => !empty($watchers_phones), 'phones' => $watchers_phones));
        return true;
    }

    protected function whats_app_sender($data)
    {
        $phone = trim($data['phone']);
        $message = strval($data['message']);
        $url = 'https://chatter.salebot.pro/api/d84cab71ac0f6f9ebcb329c24031f5fc/whatsapp_message';

        if (!$message || !$phone) {
            json(array('result' => 0, 'note' => 'Пустое сообщение либо пустой телефон', 'data' => $data));
            return false;
        }

        $query = array('phone' => $phone, 'message' => $message);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);

        $response = curl_exec($ch);
	$error = null;
	$response_info = curl_getinfo($ch);

        if(curl_errno($ch) !== 0 || $response_info['http_code'] !== 200) {
            $error = 'При выполнении запроса произошла ошибка';
        }

        $info = curl_getinfo($ch);

        curl_close($ch);

        json(array(
            'phone' => $phone,
            'message' => $message,
            'result' => $response,
            'error' => $error,
        ));
        return 1;
    }

    protected function sms_sender($data)
    {
        $vebinar_id = intval($data['vebinar_id']);
        $message = strval($data['message']);

        if (!$vebinar_id || !$message) {
            json(array('result' => 0, 'note' => 'Не передан id, либо пустое сообщение', 'data' => $data));
            return false;
        }

        $watchers = $this->getter('vebinar_watchers_new', array('vebinar_id' => $vebinar_id, 'sms_reminder' => 1), 'phone');
	$watchers_phones = fetch_to_array($watchers, 'phone');

        foreach ($watchers_phones as $i => $phone)
		$watchers_phones[$i] = preg_replace('/[^\d]/', '', $phone);

        $watchers_phones = array_unique($watchers_phones);

        include dirname(__DIR__) . "/lib/smsc_api.php";
        $result = send_sms_mail(implode(',', $watchers_phones), $message);
        json($result);
        return 1;
    }


    public function indexPageVebinars()
    {
        $sql = 'SELECT  
            `c`.`pagetitle`, 
            `c`.`id`,
            IF(`c`.`alias` = "", `c`.`id`, `c`.`alias`) as `uri`,
            `v1`.`value` as `date`,
            `v2`.`value` as `url`
         FROM `modx_site_content` as `c`
            LEFT JOIN `modx_site_tmplvar_contentvalues` as `v1`
                ON `c`.`id` = `v1`.`contentid` AND `v1`.`tmplvarid` = 140
            LEFT JOIN `modx_site_tmplvar_contentvalues` as `v2`
                ON `c`.`id` = `v2`.`contentid` AND `v2`.`tmplvarid` = 142
         WHERE 
            `c`.`parent` = 7682419 AND 
            `c`.`published` = 1 AND 
            `v1`.`value` < NOW()
         ORDER BY `v1`.`value` DESC
         LIMIT 4';
        $fetch = database::getInstance()->Select($sql);
        foreach ($fetch as $row) {
            if (isset($_GET['debug'])) {
                echo "<script>console.log('".json_encode($row)."');</script>";
            }
            $video_id = end(explode('/', $row['url']));
            $video_id = preg_replace('/\?.*/', '', $video_id);
            echo '<a href = "/' . $row['uri'] . '" title = "' . str_replace('"', "'", $row['pagetitle']) . '">
                        <img src ="//img.youtube.com/vi/' . $video_id . '/mqdefault.jpg" alt = "" >
                    </a>';
        }
        return true;
    }

    public function indexPageFutureVebinars()
    {
        $sql = 'SELECT  
            `c`.`pagetitle`, 
            `c`.`id`,
            IF(`c`.`alias` = "", `c`.`id`, `c`.`alias`) as `uri`,
            `v1`.`value` as `date`,
            `v2`.`value` as `url`
         FROM `modx_site_content` as `c`
            LEFT JOIN `modx_site_tmplvar_contentvalues` as `v1`
                ON `c`.`id` = `v1`.`contentid` AND `v1`.`tmplvarid` = 140
            LEFT JOIN `modx_site_tmplvar_contentvalues` as `v2`
                ON `c`.`id` = `v2`.`contentid` AND `v2`.`tmplvarid` = 142
         WHERE 
            `c`.`parent` = 7682419 AND 
            `c`.`published` = 1 AND 
            `v1`.`value` > NOW()
         ORDER BY `v1`.`value` ASC';
        $fetch = database::getInstance()->Select($sql);

        $vebinars_table = '<table width="" style="text-align: left; margin: 0 auto;">';

        foreach ($fetch as $row) {
            $date = date_rus_format($row['date']);
            $vebinars_table .= <<<TR
<tr>
	<td style="white-space: nowrap; padding: 7px 20px 7px 0;" valign="top">
		<strong style="color:#2198b4">—</strong>&nbsp;&nbsp;&nbsp;$date
	</td>
	<td style="padding: 7px 0; max-width: 383px">
	    <a href="/$row[uri]" target="_blank" style="display: block; width: 100%; max-width: 100%"><b> $row[pagetitle]</b></a>
	</td>
</tr>
TR;
        }
        $vebinars_table .= '<tr>
        <td style="padding: 7px 20px 7px 0;" valign="top"></td>
        <td style="padding: 7px 0; max-width: 383px">
            <a href="/education" target="_blank" style="display: block; width: 100%; max-width: 100%">
                <b> Все вебинары</b>
            </a>
        </td>
    </tr>
</table>';
        echo $vebinars_table;
        return true;
    }


    protected function company_list($data)
    {
        $text = trim((string)$data['text']);
        new Webinar(array('db-config' => $GLOBALS['revo']));
        $sql = <<<SQL
SELECT `companyName`
FROM `inn`
WHERE `companyName` LIKE "%$text%"
SQL;
        $companyList = database::getInstance()->Select($sql);
        json(fetch_to_array($companyList, 'companyName'));

    }

}

fetch_init('Vebinar');
