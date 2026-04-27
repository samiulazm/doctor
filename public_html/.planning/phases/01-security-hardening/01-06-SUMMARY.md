# Plan 01-06 Summary: OpenAI Env Key

## Completed
- Added `application/config/openai.php` with `OPENAI_API_KEY`, chat model, and vision model config values.
- Updated AI consumers to read `openai_api_key` from config instead of `settings.chatgpt_api_key`.

## Verification
- No non-settings module references `chatgpt_api_key`.
- `SecurityPatch` tests pass.
