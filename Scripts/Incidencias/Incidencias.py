import funciones_incidencias as fun_inc

fun_inc.inicializar_csv()

opcion_menu=0
if fun_inc.es_jefe():
    while opcion_menu != 4:
        opcion_menu=fun_inc.menu_jefe()
        fun_inc.case_jefe_menu(opcion_menu)
else:
    while opcion_menu != 3:
        opcion_menu=fun_inc.menu_emple()
        fun_inc.case_emple_menu(opcion_menu)


