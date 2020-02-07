<?php

class Cloudflaree_ext
{
    public $name = 'Cloudflaree';
    public $version = '1.0.0-rc1';
    public $description = 'Purge Cloudflare cache';
    public $settings_exist = 'y';
    public $docs_url = '';

    public $settings = [];

    public function __construct($settings = '')
    {
        $this->settings = $settings;
    }

    public function activate_extension()
    {
        $this->settings = [
            'zone_id' => '',
            'token' => '',
        ];

        // https://docs.expressionengine.com/latest/development/extension-hooks/model/channel-entry.html
        ee()->db->insert('extensions', [
            'class' => __CLASS__,
            'method' => 'purge',
            'hook' => 'after_channel_entry_save',
            'settings' => serialize($this->settings),
            'priority' => 10,
            'version' => $this->version,
            'enabled' => 'y',
        ]);

        // https://docs.expressionengine.com/latest/development/extension-hooks/model/template.html
        ee()->db->insert('extensions', [
            'class' => __CLASS__,
            'method' => 'purge',
            'hook' => 'after_template_save',
            'settings' => serialize($this->settings),
            'priority' => 10,
            'version' => $this->version,
            'enabled' => 'y',
        ]);
    }

    public function disable_extension()
    {
        ee()->db->where('class', __CLASS__);
        ee()->db->delete('extensions');
    }

    public function settings()
    {
        $settings = [];

        $settings['zone_id'] = ['i', '', ''];
        $settings['token'] = ['i', '', ''];

        return $settings;
    }

    public function purge()
    {
        $handler = curl_init();
        curl_setopt($handler, CURLOPT_URL, 'https://api.cloudflare.com/client/v4/zones/'.$this->settings['zone_id'].'/purge_cache');
        curl_setopt($handler, CURLOPT_POST, 1);
        curl_setopt($handler, CURLOPT_POSTFIELDS, '{"purge_everything":true}');
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handler, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->settings['token'],
        ]);

        curl_exec($handler);
        curl_close($handler);
    }
}
