<?php

namespace App\Services;

use Curio\SdClient\Facades\SdApi;

class SdApiService
{
    /*
        e.g:
        [{"id":1,"name":"docenten","type":"group","date_start":"2017-08-01","date_end":"2100-07-31","created_at":null,"updated_at":"2020-10-24T19:21:20.000000Z"},{"id":32,"name":"externen","type":"group","date_start":"2017-08-01","date_end":"2100-07-31","created_at":"2018-10-05T06:21:00.000000Z","updated_at":"2020-10-24T19:21:35.000000Z"},{"id":2,"name":"RIO4-AMO1A","type":"class","date_start":"2017-08-01","date_end":"2018-07-31","created_at":"2017-10-29T12:24:19.000000Z","updated_at":"2017-10-29T12:24:19.000000Z"}]
    */
    public function getGroups()
    {
        $groups = SdApi::get('groups');

        return $groups;
    }

    /*
        e.g:
        {"id":"xy10","name":"John Doe","email":"xy10@example.nl","email_verified_at":null,"password_force_change":null,"type":"teacher","created_at":"2017-11-01T09:01:26.000000Z","updated_at":"2024-07-04T11:32:52.000000Z","groups":[{"id":1,"name":"docenten","type":"group","date_start":"2017-08-01","date_end":"2100-07-31","created_at":null,"updated_at":"2020-10-24T19:21:20.000000Z","pivot":{"user_id":"tl10","group_id":1}}]}
    */
    public function getPersonalInfo()
    {
        $personalInfo = SdApi::get('me');

        return $personalInfo;
    }
}
