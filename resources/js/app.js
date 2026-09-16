const ADMIN_API_BASE_URL = '/api/v1';

function adminApiHeaders(json = false) {
	const headers = { Accept: 'application/json' };
	const token = window.localStorage.getItem('access_token');

	if (json) {
		headers['Content-Type'] = 'application/json';
	}

	if (token) {
		headers.Authorization = `Bearer ${token}`;
	}

	return headers;
}

async function adminApiRequest(path, options = {}) {
	const response = await fetch(`${ADMIN_API_BASE_URL}${path}`, {
		...options,
		headers: {
			...adminApiHeaders(options.body && !(options.body instanceof FormData)),
			...(options.headers || {}),
		},
	});

	const contentType = response.headers.get('content-type') || '';
	const data = contentType.includes('application/json')
		? await response.json()
		: null;

	if (!response.ok) {
		const error = new Error(data?.message || 'Không thể hoàn tất yêu cầu.');
		error.code = data?.errors?.error_code || data?.error_code || 'UNKNOWN_ERROR';
		error.data = data;
		throw error;
	}

	return { response, data };
}

window.AdminApi = {
	baseUrl: ADMIN_API_BASE_URL,
	headers: adminApiHeaders,
	request: adminApiRequest,
};
