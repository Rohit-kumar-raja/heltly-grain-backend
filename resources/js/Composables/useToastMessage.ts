import { useToast } from "primevue/usetoast";

export function useToastMessage() {
    const toast = useToast();

    const onSuccess = (detail: string) => {
        toast.add({ severity: 'success', summary: 'Successful', detail, life: 3000 });
    };

    const onError = (detail: string) => {
        toast.add({ severity: 'error', summary: 'Error', detail, life: 3000 });
    };

    const onWarning = (detail: string) => {
        toast.add({ severity: 'warn', summary: 'Warning', detail, life: 3000 });
    };

    return {
        onSuccess,
        onError,
        onWarning
    };
}
