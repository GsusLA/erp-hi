import Vue from 'vue';
import Vuex from 'vuex';
import auth from './modules/auth';

//CADECO
import obras from './modules/cadeco/obras';
import almacen from './modules/cadeco/almacen';
import concepto from './modules/cadeco/concepto';
import costo from './modules/cadeco/costo';
import cuenta from './modules/cadeco/cuenta';
import empresa from './modules/cadeco/empresa';
import fondo from './modules/cadeco/fondo';
import material from './modules/cadeco/material';

//CONTABILIDAD
import cuentaAlmacen from './modules/contabilidad/cuenta-almacen';
import cuentaBanco from './modules/contabilidad/cuenta-banco';
import cuentaConcepto from './modules/contabilidad/cuenta-concepto';
import cuentaCosto from './modules/contabilidad/cuenta-costo';
import cuentaEmpresa from './modules/contabilidad/cuenta-empresa';
import cuentaFondo from './modules/contabilidad/cuenta-fondo';
import cuentaGeneral from './modules/contabilidad/cuenta-general';
import cuentaMaterial from './modules/contabilidad/cuenta-material';
import estatusPrepoliza from './modules/contabilidad/estatus-prepoliza';
import poliza from './modules/contabilidad/poliza';
import tipoCuentaContable from './modules/contabilidad/tipo-cuenta-contable';
import tipoCuentaEmpresa from './modules/contabilidad/tipo-cuenta-empresa';
import tipoCuentaMaterial from './modules/contabilidad/tipo-cuenta-material';
import tipoPolizaContpaq from './modules/contabilidad/tipo-poliza-contpaq';

//TESORERIA
import movimientoBancario from './modules/tesoreria/movimiento-bancario';
import tipoMovimiento from './modules/tesoreria/tipo-movimiento';

//CONTRATOS
import fondoGarantia from './modules/contratos/fondo-garantia';
import solicitudMovimientoFG from './modules/contratos/solicitud-movimiento-fg';
import subcontrato from './modules/contratos/subcontrato';

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        auth,
        'cadeco/obras': obras,
        'cadeco/almacen': almacen,
        'cadeco/concepto': concepto,
        'cadeco/costo': costo,
        'cadeco/cuenta': cuenta,
        'cadeco/empresa': empresa,
        'cadeco/fondo': fondo,
        'cadeco/material': material,
        'contabilidad/cuenta-almacen': cuentaAlmacen,
        'contabilidad/cuenta-banco' : cuentaBanco,
        'contabilidad/cuenta-costo' : cuentaCosto,
        'contabilidad/cuenta-concepto' : cuentaConcepto,
        'contabilidad/cuenta-empresa' : cuentaEmpresa,
        'contabilidad/cuenta-fondo' : cuentaFondo,
        'contabilidad/cuenta-general': cuentaGeneral,
        'contabilidad/cuenta-material' : cuentaMaterial,
        'contabilidad/estatus-prepoliza': estatusPrepoliza,
        'contabilidad/poliza': poliza,
        'contabilidad/tipo-cuenta-contable': tipoCuentaContable,
        'contabilidad/tipo-cuenta-empresa': tipoCuentaEmpresa,
        'contabilidad/tipo-cuenta-material': tipoCuentaMaterial,
        'contabilidad/tipo-poliza-contpaq': tipoPolizaContpaq,

        'tesoreria/movimiento-bancario': movimientoBancario,
        'tesoreria/tipo-movimiento': tipoMovimiento,

        'contratos/fondo-garantia': fondoGarantia,
        'contratos/solicitud-movimiento-fg': solicitudMovimientoFG,
        'contratos/subcontrato': subcontrato,
    },
    strict: process.env.NODE_ENV !== 'production'
})