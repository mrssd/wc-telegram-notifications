export default {
  async fetch(request, env, ctx) {
    if (request.method !== 'POST') {
      return new Response('Method Not Allowed', { status: 405 });
    }

    const contentType = request.headers.get('content-type') || '';
    let params = {};

    try {
      if (contentType.includes('application/json')) {
        params = await request.json();
      } else if (contentType.includes('application/x-www-form-urlencoded')) {
        const formData = await request.formData();
        for (const [key, value] of formData.entries()) {
          params[key] = value;
        }
      } else {
        return new Response('Unsupported Content-Type', { status: 400 });
      }
    } catch (err) {
      return new Response('Invalid request body', { status: 400 });
    }

    const { token, method, chat_id, topic_id, parse_mode, text } = params;

    if (!token || !method || !chat_id || !text) {
      return new Response('Missing required parameters', { status: 400 });
    }

    const payload = {
      chat_id,
      text,
    };

    if (parse_mode) {
      payload.parse_mode = parse_mode;
    }

    if (topic_id) {
      payload.message_thread_id = topic_id;
    }

    const telegramUrl = `https://api.telegram.org/bot${token}/${method}`;

    try {
      const telegramResponse = await fetch(telegramUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
      });

      const responseText = await telegramResponse.text();
      return new Response(responseText, {
        status: telegramResponse.status,
        headers: { 'Content-Type': 'application/json' },
      });
    } catch (error) {
      return new Response(`Error: ${error.message}`, { status: 500 });
    }
  },
};