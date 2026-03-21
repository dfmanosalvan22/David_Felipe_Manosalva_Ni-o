import tkinter as tk
from tkinter import filedialog, messagebox, scrolledtext
import paramiko
import os

# ---------------- Configuración SSH ----------------
SSH_HOST = "192.168.100.86"
SSH_PORT = 22
SSH_USER = "ubuntu_server_docker"
SSH_KEY_PATH = "C:/Users/dfman/.ssh/id_rsa_logi" 
SCRIPT_REMOTO = "/home/ubuntu_server_docker/backup.sh"

# ---------------- Funciones ----------------
def escribir_log(mensaje):
    log.config(state="normal")
    log.insert(tk.END, f"{mensaje}\n")
    log.see(tk.END)
    log.config(state="disabled")
    root.update()

def ejecutar_backup(tipo):
    carpeta_local = filedialog.askdirectory(title="Selecciona dónde guardar el backup")
    if not carpeta_local:
        return

    escribir_log(f"Iniciando backup tipo: {tipo}")
    escribir_log(f"Destino local: {carpeta_local}")

    try:
        ssh = paramiko.SSHClient()
        ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        escribir_log(f"Conectando a {SSH_HOST}...")
        ssh.connect(SSH_HOST, port=SSH_PORT, username=SSH_USER, key_filename=SSH_KEY_PATH)
        escribir_log("Conexión SSH establecida")

        # Ejecutar script remoto
        stdin, stdout, stderr = ssh.exec_command(f"{SCRIPT_REMOTO} {tipo}")
        salida = stdout.read().decode().strip()
        errores = stderr.read().decode().strip()

        if errores:
            escribir_log(f"Advertencia: {errores}")

        if salida.startswith("EXITO:"):
            # El archivo completo está en la ruta remota indicada por el script
            archivo_remoto = salida.split(":", 1)[1].strip()
            nombre_archivo = os.path.basename(archivo_remoto)
            destino_local = os.path.join(carpeta_local, nombre_archivo)

            escribir_log(f"Archivo generado en el servidor: {archivo_remoto}")
            escribir_log("Descargando archivo...")

            # Descargar usando SFTP
            sftp = ssh.open_sftp()
            try:
                sftp.get(archivo_remoto, destino_local)
            except FileNotFoundError:
                escribir_log("ERROR: El archivo remoto no existe o la ruta es incorrecta.")
                messagebox.showerror("Error", f"No se encontró el archivo remoto:\n{archivo_remoto}")
                sftp.close()
                ssh.close()
                return
            sftp.close()

            escribir_log(f"Archivo guardado en: {destino_local}")
            messagebox.showinfo("Éxito", f"Backup completado:\n{destino_local}")
        else:
            escribir_log(f"Error en el servidor: {salida}")
            messagebox.showerror("Error", salida)

        ssh.close()

    except Exception as e:
        escribir_log(f"Error: {e}")
        messagebox.showerror("Error de conexión", str(e))


# ---------------- Interfaz Tkinter ----------------
root = tk.Tk()
root.title("Gestión de Backups Docker")
root.geometry("600x400")
root.resizable(False, False)

tk.Label(root, text="Sistema de Copias de Seguridad", font=("Arial", 14, "bold")).pack(pady=10)

frame_botones = tk.Frame(root)
frame_botones.pack(pady=5)

btn_sql = tk.Button(frame_botones, text="Backup SQL", width=25, command=lambda: ejecutar_backup("sql"))
btn_sql.pack(side="left", padx=10)

btn_vol = tk.Button(frame_botones, text="Backup Volumen Docker", width=25, command=lambda: ejecutar_backup("volumen"))
btn_vol.pack(side="left", padx=10)

tk.Label(root, text="Log de operaciones").pack(pady=5)
log = scrolledtext.ScrolledText(root, height=15, state="disabled")
log.pack(fill="both", expand=True, padx=10, pady=5)

root.mainloop()
