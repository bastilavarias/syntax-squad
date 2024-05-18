import { format } from "date-fns";

export function useCustomComposable() {
    return {
        debounce(fn, delay) {
            let timeout;

            return (...args) => {
                if (timeout) {
                    clearTimeout(timeout);
                }

                timeout = setTimeout(() => {
                    fn(...args);
                }, delay);
            };
        },

        toImageURL(image) {
            let src = "";
            if (image) {
                if (image.path) {
                    src = `${import.meta.env.VITE_BASE_API_URL}/storage/${
                        image.path
                    }`;
                } else {
                    src = image.url;
                }
            }
            return src;
        },

        limitString(string, count) {
            if (string.length <= count) {
                return string;
            } else {
                return string.substring(0, count) + "...";
            }
        },

        toLocalTime(utc) {
            const date = new Date(utc);
            const year = date.getUTCFullYear();
            const month = String(date.getUTCMonth() + 1).padStart(2, "0");
            const day = String(date.getUTCDate()).padStart(2, "0");
            const hours = String(date.getUTCHours()).padStart(2, "0");
            const minutes = String(date.getUTCMinutes()).padStart(2, "0");
            const seconds = String(date.getUTCSeconds()).padStart(2, "0");
            console.log(
                `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`
            );
            return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
        },
    };
}
