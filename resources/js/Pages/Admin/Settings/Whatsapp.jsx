import AdminLayout from "@/Layouts/AdminLayout";
import { useForm } from "@inertiajs/react";
import { Icon } from "@iconify/react";

export default function Whatsapp({ whatsapp }) {
    const { data, setData, post, processing, errors } = useForm({
        value: whatsapp?.value ?? "",
    });

    const previewUrl = data.value
        ? `https://wa.me/${data.value.replace(/\D/g, "")}`
        : null;

    const submit = (event) => {
        event.preventDefault();
        post("/admin/settings/whatsapp");
    };

    return (
        <AdminLayout>
            <div className="max-w-3xl">
                <div className="mb-6">
                    <h1 className="text-2xl font-bold text-gray-800">Whatsapp flotante</h1>
                    <p className="mt-1 text-sm text-gray-500">
                        Configura el número que usará el botón flotante del sitio. Escribe el código de país y el número completo.
                    </p>
                </div>

                <form onSubmit={submit} className="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                    <div className="grid gap-6">
                        <div>
                            <label className="mb-2 block text-sm font-medium text-gray-700">
                                Número de Whatsapp
                            </label>
                            <input
                                type="text"
                                value={data.value}
                                onChange={(event) => setData("value", event.target.value)}
                                placeholder="+54 11 2116 5379"
                                className="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-800 outline-none transition focus:border-[#25A7CA] focus:ring-4 focus:ring-[#25A7CA]/10"
                            />
                            <p className="mt-2 text-xs text-gray-400">
                                Ejemplo: <span className="font-medium text-gray-500">+54 11 2116 5379</span>. Puede incluir espacios, guiones o paréntesis.
                            </p>
                            {errors.value && (
                                <p className="mt-2 text-sm text-red-500">{errors.value}</p>
                            )}
                        </div>

                        <div className="rounded-2xl border border-sky-100 bg-sky-50 p-4">
                            <div className="flex items-start gap-3">
                                <div className="mt-0.5 flex h-10 w-10 items-center justify-center rounded-xl bg-[#25A7CA]/10">
                                    <Icon icon="solar:chat-round-line-line-duotone" width={20} className="text-[#25A7CA]" />
                                </div>
                                <div className="flex-1">
                                    <p className="text-sm font-semibold text-gray-800">Vista previa del enlace</p>
                                    <p className="mt-1 text-sm text-gray-500">
                                        El botón flotante abrirá este enlace:
                                    </p>
                                    <p className="mt-2 break-all rounded-xl bg-white px-3 py-2 text-sm text-gray-700">
                                        {previewUrl || "Completa el número para ver el enlace"}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div className="flex justify-end">
                            <button
                                type="submit"
                                disabled={processing}
                                className="inline-flex items-center gap-2 rounded-xl bg-[#25A7CA] px-5 py-2.5 text-sm font-medium text-white transition hover:bg-[#1c98bc] disabled:opacity-60"
                            >
                                {processing ? (
                                    <>
                                        <Icon icon="svg-spinners:ring-resize" width={15} />
                                        Guardando...
                                    </>
                                ) : (
                                    <>
                                        <Icon icon="solar:check-circle-outline" width={16} />
                                        Guardar número
                                    </>
                                )}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </AdminLayout>
    );
}
