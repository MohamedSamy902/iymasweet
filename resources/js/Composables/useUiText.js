import { usePage } from '@inertiajs/vue3';

export function useUiText() {
    const page = usePage();

    const t = (key, fallback = null, replacements = {}) => {
        let value = page.props.ui?.[key] ?? fallback ?? key;

        Object.entries(replacements).forEach(([token, replacement]) => {
            value = String(value).replaceAll(`{${token}}`, String(replacement));
        });

        return value;
    };

    return { t };
}
