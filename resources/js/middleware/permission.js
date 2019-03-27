export default function permission({from, next, router }) {
    let permisos = router.app.$session.get('permisos');

    if (permisos) {
        if (Array.isArray(router.currentRoute.meta.permission)) {
            let result = false;
            router.currentRoute.meta.permission.forEach(perm => {
                let search = permisos.find(p => {
                    return p.name == perm;
                });
                if (search) {
                    result = true;
                }
            });
            return result ? next() : next(from.path);
        }  else {
            var find = permisos.find(perm => {
                return perm.name == router.currentRoute.meta.permission;
            });
            return find ? next() : next(from.path);
        }
    }
    return next(from.path);
}