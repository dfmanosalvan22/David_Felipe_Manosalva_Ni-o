import subprocess
import os
from datetime import datetime

RUTA_CSV=r"\\SERVER01\DatosIncidencias\incidencias.csv"

def inicializar_csv():
    if not os.path.exists(RUTA_CSV):
        open(RUTA_CSV, "w").close()

def entero(mensaje):
    while True:
        valor = input(mensaje)
        if valor.lstrip('-').isdigit():  
            return int(valor)
        else:
            print("ERROR, introduce un número entero valido.")

# --------------------- Verificacion de Usuario -------------------------
def es_jefe():
    resultado=subprocess.run("whoami /groups", capture_output=True, text=True)
    return "GR-JEFES" in resultado.stdout
# -----------------------------------------------------------------------

# ------------------------ MENÚS ----------------------------------------
# -- JEFES
def menu_jefe():
    print("###################################################")
    print("------------------- Menu --------------------------")
    print("###################################################")
    print("1. Registrar Incidencias")
    print("2. Listar incidencias")
    print("3. Modificar Incidencias")
    print("4. Salir")
    print("###################################################")
        
    opcion=entero("¿Que quieres hacer?: ")
    return opcion

# -- EMPLEADOS
def menu_emple():
    print("###################################################")
    print("------------------- Menu --------------------------")
    print("###################################################")
    print("1. Registrar Incidencias")
    print("2. Listar mis incidencias")
    print("3. Salir")
    print("###################################################")

    opcion=entero("¿Que quieres hacer?: ") 
    return opcion

# --- Registro de Incidencias
def reg_inci():
    print("###################################################")
    print("------------------- Menu --------------------------")
    print("###################################################")
    print("1. Incidencia: Retraso en las Entregas")
    print("2. Incidencia: Daños en la Mercancia")
    print("3. Incidencia: Problemas con Clientes")
    print("4. Salir")
    print("###################################################")
        
    tipo_inci=entero("¿Que quieres hacer?: ")
    return tipo_inci

# ---- modificar
def menu_mod():
    print("###################################################")
    print("------------------- Menu --------------------------")
    print("###################################################")
    print("1. Actualizar Estado")
    print("2. Agregar Nota")
    print("3. Cerrar Incidencia")
    print("4. Generar Informe")
    print("5. Salir")
    print("###################################################")
        
    opcion=entero("¿Que quieres hacer?: ")
    return opcion
# -----------------------------------------------------------------------

# ----------------- Case para cada tipo de usuario ----------------------
# -- JEFES
def case_jefe_menu(opcion):
    match opcion:
        case 1:
            print("Registrar Incidencias")
            tipo=reg_inci()
            case_inci(tipo)
        case 2:
            print("Listar Incidencias")
            listar_inci()
        case 3:
            print("Modificar Incidencias")
            tipo=modificar_incidencia()
        case 4:
            print("Adios")
        case _:
            print("ERROR: Opcion no valida")

# -- EMPLEADOS
def case_emple_menu(opcion):
    match opcion:
        case 1:
            print("Registrar Incidencias")
            tipo=reg_inci()
            case_inci(tipo)
        case 2:
            print("Listar mis Incidencias")
            listar_inci()
        case 3:
            print("Adios")
        case _:
            print("ERROR: Opcion no valida")

# ----
def case_inci(opcion):
    TIPOS_INCIDENCIA = {
        1: "Retraso en las Entregas",
        2: "Daños en la Mercancia",
        3: "Problemas con Clientes",
        4: "Salir"
    }
    tipo_texto = TIPOS_INCIDENCIA.get(opcion, "Opción No válida")

    if opcion in [1, 2, 3]:
        usuario_actual = os.environ.get("USERNAME")
        fecha_actual = datetime.now().strftime("%Y-%m-%d %H:%M")
        estado = "Abierta"
        
        try:
            with open(RUTA_CSV, "r") as csv:
                lineas=csv.readlines()
                if len(lineas) > 0:
                    ultima_linea=lineas[-1]
                    ultimo_id=int(ultima_linea.split(",")[0])
                else:
                    ultimo_id=0
        except FileNotFoundError:
            ultimo_id=0

        nuevo_id=ultimo_id+1

        with open(RUTA_CSV, "a") as csv:
            csv.write(f"{nuevo_id},{usuario_actual},{tipo_texto},{estado},{fecha_actual},\n")
        
        print(f"Incidencia registrada: {tipo_texto} (ID: {nuevo_id})")

    elif opcion == 4:
        print("Volviendo al menu anterior")
    else:
        print(tipo_texto)

# ------------------------ Listar Incidencias ----------------------------------
def listar_inci():
    usuario_actual = os.environ.get("USERNAME")

    with open(RUTA_CSV, "r") as csv:
        lineas=csv.readlines()
        if len(lineas) > 0:
            for i in lineas:
                datos = i.strip().split(",")
                id_inci = datos[0]
                usuario_linea = datos[1]
                tipo = datos[2]
                estado = datos[3]
                fecha = datos[4]

                if es_jefe():
                    print(f"ID: {id_inci} | Usuario: {usuario_linea} | Tipo: {tipo} | Estado: {estado} | Fecha: {fecha}")
                elif usuario_actual == usuario_linea:
                    print(f"ID: {id_inci} | Usuario: {usuario_linea} | Tipo: {tipo} | Estado: {estado} | Fecha: {fecha}")
        else:
            print("No hay ninguna incidencia")
# -------------------------------------------------------------------------------

# ------------------------- Modificar Incidencias ---------------------------------
def cambiar_estado():
    id_modificar=input("ID de Incidencia a Modificar: ")
    ESTADOS_VALIDOS = ["Abierta", "En proceso", "Resuelta", "Cerrada"]
    nuevo_estado=input("Nuevo estado (Abierta / En proceso / Resuelta / Cerrada): ")
    
    if nuevo_estado not in ESTADOS_VALIDOS:
        print("Estado no valido")
        return

    with open(RUTA_CSV, "r") as csv:
        lineas=csv.readlines()
    nuevas_lineas=[]
    if len(lineas) > 0:
        cambio=False
        for i in lineas:
            datos = i.strip().split(",")
            if datos[0] == id_modificar:
                while len(datos) < 6:
                    datos.append("")
                datos[3] = nuevo_estado
                cambio=True
            nuevas_linea = ",".join(datos) + "\n"
            nuevas_lineas.append(nuevas_linea)

        with open(RUTA_CSV, "w") as csv:
            csv.writelines(nuevas_lineas)
        if cambio == True:
            print("Estado actualizado correctamente")
        else:
            print("El ID no existe en las incidencias")
# ----------------------------------------------------------------------------------

# ------------------------------ Añadir nota ----------------------------------------
def agregar_nota():
    id_modificar = input("ID de incidencia a Modificar: ")
    nota = input("Ingrese la nota: ")

    with open(RUTA_CSV, "r") as csv:
        lineas = csv.readlines()
    nuevas_lineas = []
    cambio = False
    for i in lineas:
        datos = i.strip().split(",")
        if datos[0] == id_modificar:
            while len(datos) < 6:
                datos.append("")
            datos[5] = nota
            cambio = True
        nueva_linea = ",".join(datos) + "\n"
        nuevas_lineas.append(nueva_linea)

    with open(RUTA_CSV, "w") as csv:
        csv.writelines(nuevas_lineas)
    if cambio == True:
        print("Nota agregada correctamente.")
    else:
        print("El ID no existe en las incidencias.")
# ----------------------------------------------------------------------------------

# ------------------------------- Cerrar Incidencias -------------------------------
def cerrar_incidencia():
    id_modificar = input("ID de Incidencia a Cerrar: ")
    with open(RUTA_CSV, "r") as csv:
        lineas = csv.readlines()
    nuevas_lineas = []
    cambio = False

    for i in lineas:
        datos = i.strip().split(",")
        if datos[0] == id_modificar:
            while len(datos) < 6:
                datos.append("")
            if datos[3] == "Cerrada":
                print("La incidencia ya está cerrada")
                return
            datos[3] = "Cerrada"
            cambio = True

        nueva_linea = ",".join(datos) + "\n"
        nuevas_lineas.append(nueva_linea)

    with open(RUTA_CSV, "w") as csv:
        csv.writelines(nuevas_lineas)
    if cambio:
        print("Incidencia cerrada correctamente")
    else:
        print("El ID no existe")
# -------------------------------------------------------------------------------------------

# ------------------------------ Generar Informes ------------------------------------------
def generar_informe():
    with open(RUTA_CSV, "r") as archivo:
        lineas = archivo.readlines()
    total = 0
    abiertas = 0
    proceso = 0
    resueltas = 0
    cerradas = 0
    for linea in lineas:
        datos = linea.strip().split(",")
        total += 1
        if datos[3] == "Abierta":
            abiertas += 1
        elif datos[3] == "En proceso":
            proceso += 1
        elif datos[3] == "Resuelta":
            resueltas += 1
        elif datos[3] == "Cerrada":
            cerradas += 1

    print("######################")
    print("------ INFORME ------")
    print("######################")
    print(f"Total: {total}")
    print(f"Abiertas: {abiertas}")
    print(f"En proceso: {proceso}")
    print(f"Resueltas: {resueltas}")
    print(f"Cerradas: {cerradas}")
    print("######################")
# --------------------------------------------------------------------------------------

# --------------------------- Modificar ------------------------------------------------
def modificar_incidencia():
    opcion = menu_mod()
    if opcion == 1:
        cambiar_estado()
    elif opcion == 2:
        agregar_nota()
    elif opcion == 3:
        cerrar_incidencia()
    elif opcion == 4:
        generar_informe()
    elif opcion == 5:
        print("Volviendo al menu anterior")
    else:
        print("Opción invalida")
#--------------------------------------------------------------------------
