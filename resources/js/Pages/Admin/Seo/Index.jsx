import AdminLayout from "@/Layouts/AdminLayout";
import { useForm } from "@inertiajs/react";
import { Icon } from "@iconify/react";
import { useEffect, useState } from "react";

function isPageConfigured(page) {
    return [
        page.meta_title,
        page.meta_description,
        page.meta_keywords,
        page.canonical_url,
        page.og_image_url,
    ].some((value) => (typeof value === "string" ? value.trim() : Boolean(value)));
}

function MetaForm({ page, onSaved }) {
    const [open, setOpen] = useState(false);
    const { data, setData, post, processing, errors } = useForm({
        meta_title: page.meta_title || "",
        meta_description: page.meta_description || "",
        meta_keywords: page.meta_keywords || "",
        canonical_url: page.canonical_url || "",
        robots: page.robots || "index,follow",
        og_image: null,
        remove_og_image: false,
    });

    const [previewOg, setPreviewOg] = useState(page.og_image_url || null);

    useEffect(() => {
        setData("meta_title", page.meta_title || "");
        setData("meta_description", page.meta_description || "");
        setData("meta_keywords", page.meta_keywords || "");
        setData("canonical_url", page.canonical_url || "");
        setData("robots", page.robots || "index,follow");
        setData("og_image", null);
        setData("remove_og_image", false);
        setPreviewOg(page.og_image_url || null);
    }, [page, setData]);

    const submit = (event) => {
        event.preventDefault();

        post(`/admin/seo/${page.id}`, {
            forceFormData: true,
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                onSaved?.({
                    ...page,
                    meta_title: data.meta_title.trim(),
                    meta_description: data.meta_description.trim(),
                    meta_keywords: data.meta_keywords.trim(),
                    canonical_url: data.canonical_url.trim(),
                    robots: data.robots || "index,follow",
                    og_image_url: data.remove_og_image ? null : previewOg,
                });
            },
        });
    };

    const configured = isPageConfigured({
        meta_title: data.meta_title,
        meta_description: data.meta_description,
        meta_keywords: data.meta_keywords,
        canonical_url: data.canonical_url,
        og_image_url: previewOg,
    });

    return (
        <div className="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
            <button
                type="button"
                onClick={() => setOpen((current) => !current)}
                className="flex w-full items-center justify-between px-5 py-4 transition hover:bg-gray-50/50"
            >
                <div className="flex items-center gap-3">
                    <div className={`h-2.5 w-2.5 flex-shrink-0 rounded-full ${configured ? "bg-green-400" : "bg-gray-200"}`} />
                    <span className="text-sm font-medium text-gray-800">{page.label}</span>
                    {configured ? (
                        <span className="hidden max-w-xs truncate text-xs text-gray-400 sm:block">{data.meta_title.trim() || "SEO configurado"}</span>
                    ) : (
                        <span className="rounded-full border border-amber-100 bg-amber-50 px-2 py-0.5 text-xs text-amber-500">Sin configurar</span>
                    )}
                </div>
                <Icon
                    icon={open ? "solar:alt-arrow-up-outline" : "solar:alt-arrow-down-outline"}
                    width={16}
                    className="flex-shrink-0 text-gray-400"
                />
            </button>

            {open && (
                <form onSubmit={submit} className="space-y-5 border-t border-gray-100 px-5 py-5">
                    <div>
                        <label className="mb-1 block text-sm font-medium text-gray-700">Título SEO</label>
                        <input
                            type="text"
                            value={data.meta_title}
                            onChange={(event) => setData("meta_title", event.target.value)}
                            maxLength={160}
                            placeholder={`Ej: Calderas Hercal | ${page.label}`}
                            className="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#25A7CA]/30"
                        />
                        <div className="mt-1 flex items-start justify-between">
                            <p className="text-xs text-gray-400">
                                Es el título que Google muestra primero. Lo ideal es mantenerlo claro y corto.
                            </p>
                            <span className={`ml-2 flex-shrink-0 text-xs ${data.meta_title.length > 60 ? "text-amber-500" : "text-gray-300"}`}>
                                {data.meta_title.length}/60
                            </span>
                        </div>
                        {errors.meta_title && <p className="mt-1 text-xs text-red-500">{errors.meta_title}</p>}
                    </div>

                    <div>
                        <label className="mb-1 block text-sm font-medium text-gray-700">Descripción SEO</label>
                        <textarea
                            value={data.meta_description}
                            onChange={(event) => setData("meta_description", event.target.value)}
                            maxLength={320}
                            rows={3}
                            placeholder="Describe brevemente esta página para Google y para el usuario."
                            className="w-full resize-none rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#25A7CA]/30"
                        />
                        <div className="mt-1 flex items-start justify-between">
                            <p className="text-xs text-gray-400">
                                Es el texto que aparece debajo del título en los resultados de búsqueda.
                            </p>
                            <span className={`ml-2 flex-shrink-0 text-xs ${data.meta_description.length > 160 ? "text-amber-500" : "text-gray-300"}`}>
                                {data.meta_description.length}/160
                            </span>
                        </div>
                        {errors.meta_description && <p className="mt-1 text-xs text-red-500">{errors.meta_description}</p>}
                    </div>

                    <div>
                        <label className="mb-1 block text-sm font-medium text-gray-700">Palabras clave</label>
                        <input
                            type="text"
                            value={data.meta_keywords}
                            onChange={(event) => setData("meta_keywords", event.target.value)}
                            placeholder="calderas industriales, termotanques, vapor, agua caliente"
                            className="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#25A7CA]/30"
                        />
                        <p className="mt-1 text-xs text-gray-400">
                            Sepáralas por comas. Sirven como referencia interna para mantener consistencia en el contenido.
                        </p>
                    </div>

                    <div>
                        <label className="mb-1 block text-sm font-medium text-gray-700">URL canónica</label>
                        <input
                            type="url"
                            value={data.canonical_url}
                            onChange={(event) => setData("canonical_url", event.target.value)}
                            placeholder="https://tudominio.com/pagina"
                            className="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#25A7CA]/30"
                        />
                        <p className="mt-1 text-xs text-gray-400">
                            Úsala si quieres indicarle a Google cuál es la URL principal o de referencia para esta página.
                        </p>
                    </div>

                    <div>
                        <label className="mb-1 block text-sm font-medium text-gray-700">Visibilidad para buscadores</label>
                        <select
                            value={data.robots}
                            onChange={(event) => setData("robots", event.target.value)}
                            className="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#25A7CA]/30"
                        >
                            <option value="index,follow">Mostrar en Google</option>
                            <option value="noindex,follow">Ocultar en Google</option>
                            <option value="index,nofollow">Indexar sin seguir enlaces</option>
                            <option value="noindex,nofollow">No indexar ni seguir enlaces</option>
                        </select>
                        <p className="mt-1 text-xs text-gray-400">
                            En la mayoría de los casos conviene dejar “Mostrar en Google”.
                        </p>
                        {errors.robots && <p className="mt-1 text-xs text-red-500">{errors.robots}</p>}
                    </div>

                    <div>
                        <label className="mb-1 block text-sm font-medium text-gray-700">Imagen para compartir</label>
                        {previewOg && (
                            <div className="relative mb-2 inline-block">
                                <img src={previewOg} className="h-24 rounded-xl border border-gray-100 object-cover" alt="Vista previa SEO" />
                                <span className="absolute bottom-1 left-1 rounded bg-black/50 px-1.5 py-0.5 text-[10px] text-white">Vista previa</span>
                            </div>
                        )}
                        <input
                            type="file"
                            accept="image/*"
                            onChange={(event) => {
                                const file = event.target.files?.[0] ?? null;
                                setData("og_image", file);
                                if (file) {
                                    setPreviewOg(URL.createObjectURL(file));
                                    setData("remove_og_image", false);
                                }
                            }}
                            className="block w-full text-sm text-gray-500 file:mr-3 file:rounded-lg file:border-0 file:bg-[#25A7CA]/10 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-[#25A7CA]"
                        />
                        <div className="mt-2 flex items-center justify-between">
                            <p className="text-xs text-gray-400">
                                Esta imagen aparece cuando alguien comparte el link por WhatsApp, LinkedIn o Facebook.
                            </p>
                            {previewOg && (
                                <button
                                    type="button"
                                    onClick={() => {
                                        setPreviewOg(null);
                                        setData("og_image", null);
                                        setData("remove_og_image", true);
                                    }}
                                    className="text-xs font-medium text-red-500"
                                >
                                    Quitar imagen
                                </button>
                            )}
                        </div>
                    </div>

                    <div className="flex justify-end gap-3 border-t border-gray-50 pt-1">
                        <button
                            type="button"
                            onClick={() => setOpen(false)}
                            className="rounded-xl border border-gray-200 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            disabled={processing}
                            className="flex items-center gap-2 rounded-xl bg-[#25A7CA] px-5 py-2 text-sm font-medium text-white hover:bg-[#1d96b8] disabled:opacity-60"
                        >
                            {processing ? (
                                <>
                                    <Icon icon="svg-spinners:ring-resize" width={14} />
                                    Guardando...
                                </>
                            ) : (
                                <>
                                    <Icon icon="solar:check-circle-outline" width={14} />
                                    Guardar
                                </>
                            )}
                        </button>
                    </div>
                </form>
            )}
        </div>
    );
}

export default function SeoIndex({ pages }) {
    const [localPages, setLocalPages] = useState(pages);

    useEffect(() => {
        setLocalPages(pages);
    }, [pages]);

    const handleSaved = (savedPage) => {
        setLocalPages((currentPages) =>
            currentPages.map((page) => (page.id === savedPage.id ? { ...page, ...savedPage } : page))
        );
    };

    const configured = localPages.filter((page) => isPageConfigured(page)).length;

    return (
        <AdminLayout>
            <div className="max-w-4xl">
                <div className="mb-6">
                    <h1 className="text-2xl font-bold text-gray-800">SEO y redes sociales</h1>
                    <p className="mt-1 text-sm text-gray-500">
                        Configura cómo aparece cada página en Google y cómo se ve cuando alguien comparte el enlace.
                    </p>
                </div>

                <div className="mb-6 flex items-center gap-3 rounded-2xl border border-gray-100 bg-white px-5 py-4 shadow-sm">
                    <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-[#25A7CA]/10">
                        <Icon icon="solar:chart-2-outline" width={20} className="text-[#25A7CA]" />
                    </div>
                    <div>
                        <p className="text-sm font-semibold text-gray-800">
                            {configured} de {localPages.length} páginas configuradas
                        </p>
                        <p className="text-xs text-gray-400">
                            Completa cada bloque para mejorar cómo se ve el sitio en buscadores y redes sociales.
                        </p>
                    </div>
                </div>

                <div className="mb-6 grid gap-3 rounded-2xl border border-blue-100 bg-blue-50 p-5 sm:grid-cols-2">
                    {[
                        {
                            icon: "solar:magnifer-outline",
                            title: "Mejor presentación en Google",
                            desc: "El título y la descripción ayudan a que el usuario entienda rápido de qué trata tu página y haga clic.",
                        },
                        {
                            icon: "solar:smartphone-outline",
                            title: "Mejor vista al compartir enlaces",
                            desc: "La imagen para compartir se usa en WhatsApp, LinkedIn y otras redes cuando alguien pega la URL.",
                        },
                        {
                            icon: "solar:link-outline",
                            title: "Referencia correcta para buscadores",
                            desc: "La URL canónica sirve para indicar cuál es la dirección principal si hay variantes o páginas parecidas.",
                        },
                        {
                            icon: "solar:check-circle-outline",
                            title: "Todo se aplica automáticamente",
                            desc: "Lo que guardes aquí se refleja en el layout general del sitio, sin tocar cada módulo por separado.",
                        },
                    ].map((item) => (
                        <div key={item.title} className="flex gap-2.5">
                            <Icon icon={item.icon} width={16} className="mt-0.5 flex-shrink-0 text-blue-400" />
                            <div>
                                <p className="text-xs font-semibold text-blue-700">{item.title}</p>
                                <p className="text-xs leading-relaxed text-blue-600/80">{item.desc}</p>
                            </div>
                        </div>
                    ))}
                </div>

                <div className="space-y-3">
                    {localPages.map((page) => (
                        <MetaForm key={page.id} page={page} onSaved={handleSaved} />
                    ))}
                </div>
            </div>
        </AdminLayout>
    );
}
