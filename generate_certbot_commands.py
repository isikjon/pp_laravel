import sqlite3
from pathlib import Path

DATABASE = Path(__file__).parent / 'database' / 'database.sqlite'
BASE_DOMAIN = 'prostitutkimoskvytake.org'
EMAIL = 'admin@prostitutkimoskvytake.org'
BATCH_SIZE = 5

def get_active_cities():
    conn = sqlite3.connect(str(DATABASE))
    cursor = conn.cursor()
    cursor.execute("SELECT name, subdomain FROM cities WHERE is_active = 1 ORDER BY name")
    cities_data = cursor.fetchall()
    conn.close()
    
    domains = []
    for name, subdomain in cities_data:
        if subdomain:
            full_domain = f"{subdomain}.{BASE_DOMAIN}"
        else:
            full_domain = BASE_DOMAIN
        domains.append({'domain': full_domain, 'name': name})
    return domains

def generate_certbot_commands():
    domains = get_active_cities()
    
    print(f"Всего доменов: {len(domains)}")
    print(f"Будет создано команд: {(len(domains) + BATCH_SIZE - 1) // BATCH_SIZE}")
    print("\n" + "="*80)
    print("КОМАНДЫ CERTBOT ДЛЯ МАССОВОЙ УСТАНОВКИ SSL")
    print("="*80 + "\n")
    
    for i in range(0, len(domains), BATCH_SIZE):
        batch = domains[i:i+BATCH_SIZE]
        batch_num = (i // BATCH_SIZE) + 1
        
        print(f"# Команда {batch_num} из {(len(domains) + BATCH_SIZE - 1) // BATCH_SIZE}")
        print(f"# Домены: {', '.join([d['name'] for d in batch])}")
        
        domain_args = []
        for d in batch:
            domain_args.append(f"-d {d['domain']}")
            domain_args.append(f"-d www.{d['domain']}")
        
        command = f"sudo certbot --nginx --non-interactive --agree-tos --email {EMAIL} --expand {' '.join(domain_args)}"
        
        print(command)
        print("\n")

if __name__ == "__main__":
    generate_certbot_commands()

