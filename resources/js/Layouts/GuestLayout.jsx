export default function GuestLayout({ children }) {
    return (
        <div className="gs-auth">

            {/* ── Panel izquierdo: marca ── */}
            <div className="gs-auth__brand">
                <div className="gs-auth__brand-inner">
                    <div className="gs-auth__logo-shell">
                        <img
                            src="/media/logo/logo-hercal.png"
                            alt="Calderas Hercal"
                            className="gs-auth__logo"
                        />
                    </div>
                    <span className="gs-auth__brand-sub">Instrumentación Industrial</span>
                    <span className="gs-auth__brand-sub">Bienvenido al panel de administracion</span>

                </div>
            </div>

            {/* ── Panel derecho: formulario ── */}
            <div className="gs-auth__form-panel">
                <div className="gs-auth__form-box">
                    {children}
                </div>
            </div>

        </div>
    );
}
