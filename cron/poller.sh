# enable pollers

echo "Starting pollers.sh $(date) in $(pwd)"

python ~/offgrid/cron/modules/poll-network.py &
#python modules/poll-time.py &
python ~/offgrid/cron/modules/poll-ina219-power-HAT.py &

echo "Exiting pollers.sh $(date)"S
