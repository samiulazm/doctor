<?php

defined('BASEPATH') or exit('No direct script access allowed');

$config['openai_chat_model'] = getenv('OPENAI_CHAT_MODEL') ?: 'gpt-4o';
$config['openai_vision_model'] = getenv('OPENAI_VISION_MODEL') ?: 'gpt-4o';
$config['openai_api_key'] = getenv('OPENAI_API_KEY') ?: '';
