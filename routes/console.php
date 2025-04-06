<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('scrape:profiles', ['--likes-min' => config('scraping.likes_threshold')])->daily();
Schedule::command('scrape:profiles', ['--likes-max' => config('scraping.likes_threshold')])->cron('0 0 */3 * *');
