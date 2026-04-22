import AdminLayout from "@/Layouts/AdminLayout";
import { Icon } from "@iconify/react";
import { router } from "@inertiajs/react";

export default function Dashboard({ stats, recent_submissions }) {
    recent_submissions = recent_submissions ?? [];
    const cards = [
        { label: "Servicios", value: stats.services, icon: "solar:settings-outline", color: "bg-sky-50 text-sky-600", url: "/admin/services" },
        { label: "Equipos", value: stats.equipments, icon: "solar:box-outline", color: "bg-amber-50 text-amber-600", url: "/admin/equipments" },
        { label: "Proyectos", value: stats.projects, icon: "solar:folder-with-files-outline", color: "bg-violet-50 text-violet-600", url: "/admin/projects" },
        {
            label: "Solicitudes",
            value: stats.submissions,
            icon: "solar:inbox-outline",
            color: "bg-emerald-50 text-emerald-600",
            url: "/admin/submissions",
            badge: stats.unread_submissions,
        },
    ];

    return (
        <AdminLayout>
            <div className="space-y-6">
                <section className="rounded-[30px] border border-slate-200 bg-white p-6 shadow-sm">
                    <div className="flex flex-col gap-6 xl:flex-row xl:items-center xl:justify-between">
                        <div>
                            <div className="mb-3 inline-flex items-center gap-2 rounded-full bg-[#25A7CA]/10 px-3 py-1 text-xs font-semibold text-[#25A7CA]">
                                <Icon icon="solar:rocket-outline" width={14} />
                                Sitio listo para gestionar
                            </div>
                            <h1 className="text-3xl font-semibold tracking-tight text-slate-900">Dashboard</h1>
                            <p className="mt-2 max-w-2xl text-sm text-slate-500">
                                Vista rápida del contenido editable y del flujo de solicitudes que llegan desde la web pública.
                            </p>
                        </div>

                        <div className="grid gap-3 sm:grid-cols-2">
                            <button
                                onClick={() => router.visit("/admin/cms/home")}
                                className="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-left transition hover:border-[#25A7CA] hover:bg-white"
                            >
                                <p className="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Edición rápida</p>
                                <p className="mt-1 text-sm font-medium text-slate-800">Ir al Home dinámico</p>
                            </button>
                            <button
                                onClick={() => router.visit("/admin/submissions")}
                                className="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-left transition hover:border-[#25A7CA] hover:bg-white"
                            >
                                <p className="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">CRM</p>
                                <p className="mt-1 text-sm font-medium text-slate-800">Ver solicitudes nuevas</p>
                            </button>
                        </div>
                    </div>
                </section>

                <section className="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    {cards.map((card) => (
                        <button
                            key={card.label}
                            onClick={() => router.visit(card.url)}
                            className="relative w-full rounded-[26px] border border-slate-200 bg-white p-5 text-left shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
                        >
                            {card.badge > 0 && (
                                <span className="absolute right-4 top-4 rounded-full bg-red-500 px-2 py-0.5 text-[10px] font-bold text-white">
                                    {card.badge} nuevos
                                </span>
                            )}
                            <div className={`mb-4 flex h-11 w-11 items-center justify-center rounded-2xl ${card.color}`}>
                                <Icon icon={card.icon} width={20} />
                            </div>
                            <div className="text-3xl font-semibold text-slate-900">{card.value ?? 0}</div>
                            <div className="mt-1 text-sm text-slate-500">{card.label}</div>
                        </button>
                    ))}
                </section>

                <section className="overflow-hidden rounded-[30px] border border-slate-200 bg-white shadow-sm">
                    <div className="flex items-center justify-between border-b border-slate-100 px-6 py-5">
                        <div>
                            <h2 className="text-lg font-semibold text-slate-900">Últimas solicitudes</h2>
                            <p className="mt-1 text-sm text-slate-500">Entrada unificada desde contacto, presupuesto y futuros formularios.</p>
                        </div>
                        <button
                            onClick={() => router.visit("/admin/submissions")}
                            className="rounded-2xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 transition hover:border-[#25A7CA] hover:text-[#25A7CA]"
                        >
                            Ver todas
                        </button>
                    </div>
                    <div className="divide-y divide-slate-100">
                        {recent_submissions.length === 0 && (
                            <p className="px-6 py-10 text-center text-sm text-slate-400">Todavía no hay solicitudes registradas.</p>
                        )}
                        {recent_submissions.map((submission) => (
                            <div
                                key={submission.id}
                                onClick={() => router.visit(`/admin/submissions/${submission.id}`)}
                                className="flex cursor-pointer flex-col gap-3 px-6 py-4 transition hover:bg-slate-50 sm:flex-row sm:items-center"
                            >
                                <div className="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#25A7CA]/10 text-[#25A7CA]">
                                    <Icon icon="solar:inbox-line-outline" width={18} />
                                </div>
                                <div className="min-w-0 flex-1">
                                    <div className="flex flex-wrap items-center gap-2">
                                        <p className="truncate text-sm font-medium text-slate-900">{submission.name}</p>
                                        <span className="rounded-full bg-slate-100 px-2.5 py-0.5 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500">
                                            {submission.type}
                                        </span>
                                    </div>
                                    <p className="mt-1 truncate text-xs text-slate-500">{submission.email}</p>
                                    <p className="mt-1 truncate text-sm text-slate-600">{submission.message}</p>
                                </div>
                                <div className="flex items-center gap-2 self-start sm:self-center">
                                    <span className="text-xs text-slate-400">{submission.created_at}</span>
                                    {!submission.is_read && <span className="h-2.5 w-2.5 rounded-full bg-emerald-500" />}
                                </div>
                            </div>
                        ))}
                    </div>
                </section>
            </div>
        </AdminLayout>
    );
}
