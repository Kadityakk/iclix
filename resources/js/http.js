/**
 * Builds an absolute URL from the application root, so the app also works from a subfolder (XAMPP/Laragon).
 */
export function appUrl(path) {
    return `${window.iClix.baseUrl}/${path.replace(/^\//, '')}`;
}

function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

/**
 * Sends a JSON request with the CSRF token and returns the decoded response.
 *
 * @throws {Error} When the server answers with an error status.
 */
export async function requestJson(path, { method = 'GET', body, signal, keepalive = false } = {}) {
    const response = await fetch(appUrl(path), {
        method,
        signal,
        keepalive,
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: body === undefined ? undefined : JSON.stringify(body),
    });

    if (!response.ok) {
        throw new Error(`Request to ${path} failed with status ${response.status}`);
    }

    return response.json();
}

export { csrfToken };
