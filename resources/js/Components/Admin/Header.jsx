import { usePage, router } from "@inertiajs/react";
import { Icon } from "@iconify/react";

export default function Header({ onToggleSidebar }) {
    const { auth, admin } = usePage().props;
    const user = auth?.user;
    const userInitial = user?.name?.charAt(0)?.toUpperCase?.() || "A";

    return (
        <header className="flex min-h-16 items-center justify-between border-b border-slate-200 bg-white px-5 md:px-8">
            <div className="flex items-center gap-3">
                <button
                    type="button"
                    onClick={onToggleSidebar}
                    className="flex h-10 w-10 items-center justify-center rounded-2xl border border-slate-200 text-slate-500 transition hover:border-[#25A7CA] hover:text-[#25A7CA] md:hidden"
                    aria-label="Abrir menú"
                >
                    <Icon icon="solar:hamburger-menu-outline" width={20} />
                </button>

                <div>
                    <p className="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Administración</p>
                    <h2 className="text-base font-semibold text-slate-900 md:text-lg">Panel de administración</h2>
                </div>
            </div>

            <div className="flex items-center gap-3 md:gap-4">
                <button
                    onClick={() => router.visit("/admin/submissions")}
                    className="relative flex rounded-2xl border border-slate-200 p-3 text-slate-500 transition hover:border-[#25A7CA] hover:text-[#25A7CA]"
                    title="Solicitudes"
                >
                    <Icon icon="solar:bell-outline" width={18} />
                    {admin?.unreadSubmissionsCount > 0 && (
                        <span className="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-semibold text-white">
                            {admin.unreadSubmissionsCount > 99 ? "99+" : admin.unreadSubmissionsCount}
                        </span>
                    )}
                </button>

                <div className="flex items-center gap-3">
                    {user?.avatar_url ? (
                        <img
                            src={user.avatar_url}
                            alt={user.name || "Usuario"}
                            className="h-9 w-9 rounded-full object-cover"
                        />
                    ) : (
                        <div className="flex h-9 w-9 items-center justify-center rounded-full bg-[#25A7CA] text-sm font-semibold text-white">
                            {userInitial}
                        </div>
                    )}
                    <div className="hidden min-w-0 sm:block">
                        <p className="truncate text-sm text-slate-700">{user?.name}</p>
                        <p className="truncate text-xs text-slate-400">{user?.email}</p>
                    </div>
                    <button
                        onClick={() => router.post("/admin/logout")}
                        className="hidden text-sm text-red-500 hover:text-red-600 sm:block"
                    >
                        Cerrar sesión
                    </button>
                </div>
            </div>
        </header>
    );
}
