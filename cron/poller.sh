# enable pollers

echo "Starting pollers.sh : $(date)"

python ~/offgrid/cron/modules/poll-network.py &

#python ~/offgrid/cron/modules/poll-time.py &

echo "Exiting pollers.sh : $(date)"