import { router, usePage } from "@inertiajs/react";
import { Icon } from "@iconify/react";
import { useEffect, useRef } from "react";

const SIDEBAR_SCROLL_KEY = "hercal_admin_sidebar_scroll";

function SidebarItem({ item, currentUrl, onNavigate }) {
    const active = item.exact
        ? currentUrl === item.url
        : item.match
            ? currentUrl.startsWith(item.match)
            : currentUrl.startsWith(item.url);

    return (
        <button
            onClick={() => onNavigate(item.url)}
            className={`group relative flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left transition ${
                active
                    ? "bg-[#25A7CA]/10 font-medium text-[#25A7CA]"
                    : "text-gray-700 hover:bg-gray-50"
            }`}
        >
            {active && (
                <span className="absolute left-0 h-5 w-[3px] rounded-r-full bg-[#25A7CA]" />
            )}
            <Icon
                icon={item.icon}
                width={15}
                className={active ? "text-[#25A7CA]" : "text-gray-400"}
            />
            <span className="min-w-0 flex-1 truncate text-sm">{item.title}</span>
            {item.badge > 0 && (
                <span className="rounded-full bg-red-500 px-2 py-0.5 text-[11px] font-semibold text-white">
                    {item.badge}
                </span>
            )}
        </button>
    );
}

export default function Sidebar({ onNavigateComplete = () => {}, mobile = false }) {
    const page = usePage();
    const auth = page.props?.auth;
    const unreadSubmissions = page.props?.admin?.unreadSubmissionsCount ?? 0;
    const currentUrl = page.url ?? page.props?.url ?? "";
    const navRef = useRef(null);

    useEffect(() => {
        const savedScroll = window.sessionStorage.getItem(SIDEBAR_SCROLL_KEY);

        if (!navRef.current || savedScroll === null) {
            return;
        }

        navRef.current.scrollTop = Number(savedScroll) || 0;
    }, [currentUrl]);

    const handleNavigate = (url) => {
        if (navRef.current) {
            window.sessionStorage.setItem(SIDEBAR_SCROLL_KEY, String(navRef.current.scrollTop));
        }

        onNavigateComplete();
        router.visit(url);
    };

    const sections = [
        {
            title: "Panel",
            icon: "solar:home-2-outline",
            children: [
                { title: "Dashboard", icon: "solar:home-outline", url: "/admin/dashboard", match: "/admin/dashboard" },
            ],
        },
        {
            title: "Home",
            icon: "solar:home-smile-outline",
            children: [
                { title: "Sliders", icon: "solar:gallery-wide-outline", url: "/admin/cms/home/sliders", exact: true },
                { title: "Banner intermedio", icon: "solar:gallery-add-outline", url: "/admin/cms/home/banner", exact: true },
                { title: "Nosotros Home", icon: "solar:users-group-rounded-outline", url: "/admin/cms/home/nosotros-home", exact: true },
            ],
        },
        {
            title: "Nosotros",
            icon: "solar:users-group-rounded-outline",
            children: [
                { title: "Banner", icon: "solar:gallery-wide-outline", url: "/admin/cms/nosotros/banner", exact: true },
                { title: "Contenido", icon: "solar:document-text-outline", url: "/admin/cms/nosotros/contenido", exact: true },
            ],
        },
        {
            title: "Servicios",
            icon: "solar:settings-outline",
            children: [
                { title: "Banner", icon: "solar:gallery-wide-outline", url: "/admin/cms/servicios/banner", exact: true },
                { title: "Listado y edición", icon: "solar:document-text-outline", url: "/admin/services", match: "/admin/services" },
            ],
        },
        {
            title: "Equipos",
            icon: "solar:box-outline",
            children: [
                { title: "Banner", icon: "solar:gallery-wide-outline", url: "/admin/cms/equipos/banner", exact: true },
                { title: "Categorías", icon: "solar:widget-5-outline", url: "/admin/equipment-categories", match: "/admin/equipment-categories" },
                { title: "Productos", icon: "solar:document-text-outline", url: "/admin/equipments", match: "/admin/equipments" },
            ],
        },
        {
            title: "Proyectos",
            icon: "solar:folder-with-files-outline",
            children: [
                { title: "Banner", icon: "solar:gallery-wide-outline", url: "/admin/cms/trabajos-realizados/banner", exact: true },
                { title: "Listado y edición", icon: "solar:document-text-outline", url: "/admin/projects", match: "/admin/projects" },
            ],
        },
        {
            title: "Clientes",
            icon: "solar:buildings-2-outline",
            children: [
                { title: "Banner", icon: "solar:gallery-wide-outline", url: "/admin/cms/clientes/banner", exact: true },
                { title: "Contenido", icon: "solar:document-text-outline", url: "/admin/cms/clientes/contenido", exact: true },
            ],
        },
        {
            title: "Contacto",
            icon: "solar:chat-round-outline",
            children: [
                { title: "Banner", icon: "solar:gallery-wide-outline", url: "/admin/cms/contacto/banner", exact: true },
                { title: "Formulario y datos", icon: "solar:map-point-outline", url: "/admin/cms/contacto/formulario", exact: true },
                { title: "Solicitudes", icon: "solar:letter-outline", url: "/admin/submissions/contacto", exact: true },
            ],
        },
        {
            title: "Presupuesto",
            icon: "solar:document-text-outline",
            children: [
                { title: "Banner", icon: "solar:gallery-wide-outline", url: "/admin/cms/presupuesto/banner", exact: true },
                { title: "Formulario", icon: "solar:document-text-outline", url: "/admin/cms/presupuesto/formulario", exact: true },
                { title: "Solicitudes", icon: "solar:letter-outline", url: "/admin/submissions/presupuesto", exact: true },
            ],
        },
        {
            title: "Solicitudes",
            icon: "solar:inbox-outline",
            children: [
                {
                    title: "Todas las solicitudes",
                    icon: "solar:letter-outline",
                    url: "/admin/submissions",
                    exact: true,
                    badge: unreadSubmissions,
                },
            ],
        },
        {
            title: "Whatsapp flotante",
            icon: "solar:chat-round-line-line-duotone",
            children: [
                { title: "Número y enlace", icon: "solar:phone-calling-outline", url: "/admin/settings/whatsapp", exact: true },
            ],
        },
        {
            title: "SEO",
            icon: "solar:global-outline",
            children: [
                { title: "Metadatos y redes", icon: "solar:tag-outline", url: "/admin/seo", match: "/admin/seo" },
            ],
        },
        {
            title: "Usuarios",
            icon: "solar:user-id-outline",
            children: [
                { title: "Usuarios admin", icon: "solar:users-group-two-rounded-outline", url: "/admin/users", match: "/admin/users" },
            ],
        },
    ];

    return (
        <aside className={`${mobile ? "flex h-full w-full flex-col bg-white" : "sticky top-0 flex h-screen w-[272px] flex-col border-r border-gray-200 bg-white"}`}>
            <div className="flex h-16 items-center gap-3 border-b border-gray-200 px-5">
                <div className="flex h-9 w-9 items-center justify-center rounded-xl bg-[#25A7CA]">
                    <Icon icon="solar:settings-bold" width={18} color="white" />
                </div>
                <div className="leading-tight">
                    <p className="text-sm font-semibold text-gray-800">Panel Admin</p>
                    <p className="text-xs text-gray-400">Calderas Hercal</p>
                </div>
                {mobile && (
                    <button
                        type="button"
                        onClick={onNavigateComplete}
                        className="ml-auto flex h-9 w-9 items-center justify-center rounded-xl border border-gray-200 text-gray-400"
                        aria-label="Cerrar menú"
                    >
                        <Icon icon="solar:close-circle-outline" width={18} />
                    </button>
                )}
            </div>

            <nav
                ref={navRef}
                onScroll={(event) => window.sessionStorage.setItem(SIDEBAR_SCROLL_KEY, String(event.currentTarget.scrollTop))}
                className="flex-1 space-y-4 overflow-y-auto px-3 py-4"
            >
                {sections.map((section) => (
                    <div key={section.title}>
                        <div className="flex items-center gap-2.5 px-3 py-2">
                            <Icon icon={section.icon} width={16} className="text-gray-400" />
                            <span className="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                {section.title}
                            </span>
                        </div>

                        <div className="mt-1 pl-3">
                            {section.children.map((item) => (
                                <SidebarItem key={item.title} item={item} currentUrl={currentUrl} onNavigate={handleNavigate} />
                            ))}
                        </div>
                    </div>
                ))}
            </nav>

            <div className="border-t border-gray-200 p-3">
                {auth?.user && (
                    <div className="mb-2 flex items-center gap-2.5 rounded-xl bg-gray-50 p-2">
                        {auth.user.avatar_url ? (
                            <img
                                src={auth.user.avatar_url}
                                alt={auth.user.name}
                                className="h-8 w-8 flex-shrink-0 rounded-full object-cover"
                            />
                        ) : (
                            <div className="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-[#25A7CA]/20">
                                <Icon icon="solar:user-bold" width={16} className="text-[#25A7CA]" />
                            </div>
                        )}
                        <div className="min-w-0">
                            <p className="truncate text-xs font-semibold text-gray-800">{auth.user.name}</p>
                            <p className="truncate text-xs text-gray-400">{auth.user.email}</p>
                        </div>
                    </div>
                )}

                <button
                    onClick={() => router.post("/admin/logout")}
                    className="flex w-full items-center justify-center gap-2 rounded-xl border border-red-100 bg-red-50 py-2.5 text-red-500 transition hover:bg-red-100"
                >
                    <Icon icon="solar:logout-2-outline" width={15} />
                    <span className="text-sm font-medium">Cerrar sesión</span>
                </button>
            </div>
        </aside>
    );
}
