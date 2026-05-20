import tkinter as tk
from tkinter import messagebox, scrolledtext
import paramiko
import os
import subprocess

# ---------------- CONFIGURACIÓN ----------------

SSH_HOST = "54.37.159.24"
SSH_PORT = 49152

SSH_USER = "backup_servicio"

SSH_KEY_PATH = "id_ed25519_backup"

SCRIPT_REMOTO = "/home/backup_servicio/backup.sh"

CARPETA_BACKUPS = r"\\server01\Backups_Logitrans"

# ---------------- FUNCIONES ----------------

def escribir_log(mensaje):
    log.config(state="normal")
    log.insert(tk.END, f"{mensaje}\n")
    log.see(tk.END)
    log.config(state="disabled")
    root.update()

def ejecutar_backup(tipo):

    escribir_log(f"Iniciando backup tipo: {tipo}")

    try:

        ssh = paramiko.SSHClient()

        ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())

        escribir_log(f"Conectando a {SSH_HOST}...")

        ssh.connect(
            SSH_HOST,
            port=SSH_PORT,
            username=SSH_USER,
            key_filename=SSH_KEY_PATH
        )

        escribir_log("Conexión SSH establecida")

        comando = f"sudo {SCRIPT_REMOTO} {tipo}"

        stdin, stdout, stderr = ssh.exec_command(comando)

        salida = stdout.read().decode().strip()

        errores = stderr.read().decode().strip()

        if errores:
            escribir_log(f"ERROR SSH: {errores}")

        if salida.startswith("EXITO:"):

            archivo_remoto = salida.split(":", 1)[1].strip()

            nombre_archivo = os.path.basename(archivo_remoto)

            destino_local = os.path.join(
                CARPETA_BACKUPS,
                nombre_archivo
            )

            escribir_log(f"Archivo remoto: {archivo_remoto}")

            escribir_log("Descargando backup...")

            escribir_log("Montando unidad de red...")
            resultado = subprocess.run(
                r'net use \\server01\Backups_Logitrans /persistent:no',
                shell=True,
                capture_output=True,
                text=True
            )
            if resultado.returncode != 0:
                escribir_log(f"ERROR montando red: {resultado.stderr}")
                messagebox.showerror("Error", f"No se pudo acceder a la carpeta de red:\n\\\\server01\\Backups_Logitrans")
                ssh.close()
                return

            sftp = ssh.open_sftp()

            sftp.get(archivo_remoto, destino_local)

            sftp.close()

            escribir_log(f"Backup guardado en:")
            escribir_log(destino_local)

            messagebox.showinfo(
                "Éxito",
                f"Backup descargado correctamente:\n\n{destino_local}"
            )

        else:

            escribir_log(f"Error servidor: {salida}")

            messagebox.showerror(
                "Error",
                salida
            )

        ssh.close()

    except Exception as e:

        escribir_log(f"ERROR: {e}")

        messagebox.showerror(
            "Error",
            str(e)
        )

# ---------------- INTERFAZ ----------------

root = tk.Tk()

root.title("Sistema de Backups Logitrans")

root.geometry("650x400")

root.resizable(False, False)

tk.Label(
    root,
    text="Sistema de Copias de Seguridad",
    font=("Arial", 14, "bold")
).pack(pady=10)

frame_botones = tk.Frame(root)

frame_botones.pack(pady=5)

btn_sql = tk.Button(
    frame_botones,
    text="Backup SQL",
    width=25,
    command=lambda: ejecutar_backup("sql")
)

btn_sql.pack(side="left", padx=10)

btn_vol = tk.Button(
    frame_botones,
    text="Backup Volumen Docker",
    width=25,
    command=lambda: ejecutar_backup("volumen")
)

btn_vol.pack(side="left", padx=10)

tk.Label(
    root,
    text="Log de operaciones"
).pack(pady=5)

log = scrolledtext.ScrolledText(
    root,
    height=15,
    state="disabled"
)

log.pack(
    fill="both",
    expand=True,
    padx=10,
    pady=5
)

root.mainloop()
